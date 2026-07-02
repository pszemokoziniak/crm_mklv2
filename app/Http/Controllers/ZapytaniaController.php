<?php

namespace App\Http\Controllers;

use App\Http\Requests\WznowienieStoreRequest;
use App\Http\Requests\WznowienieUpdateRequest; // Import the new request
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Mail\ZapytaniaMail;
use App\Models\ArchiwumZapytania;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\Kursy;
use App\Models\Oferta;
use App\Models\User;
use App\Models\Waluta;
use App\Models\Zakres;
use App\Models\Zapytania;
use App\Models\Kontakt;
use App\Models\ZapytaniaWznowienie;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;
use Illuminate\Support\Facades\DB;

class ZapytaniaController extends Controller
{
    use StoreActivityLog;
    public function index()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $stats = [
            'total' => Zapytania::count(),
            'this_month' => Zapytania::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            'prev_month' => Zapytania::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count(),
            'with_oferta' => Zapytania::whereHas('oferty', function ($q) {
                $q->whereNull('deleted_at');
            })->count(),
            'without_oferta' => Zapytania::whereDoesntHave('oferty', function ($q) {
                $q->whereNull('deleted_at');
            })->count(),
        ];

        $sortField = Request::input('field');
        $sortDirection = Request::input('direction') === 'asc' ? 'asc' : 'desc';

        $query = Zapytania::with(['client', 'user', 'kraj', 'zakres', 'waluta', 'otrzymal', 'opracowuje'])
            ->filter(Request::only('search', 'trashed'));

        if ($sortField === 'projekt') {
            $query->orderBy('nazwa_projektu', $sortDirection);
        } elseif ($sortField === 'client') {
            $query->leftJoin('clients', 'zapytanias.client_id', '=', 'clients.id')
                ->orderBy('clients.nazwa', $sortDirection)
                ->select('zapytanias.*');
        } elseif ($sortField === 'kraj') {
            $query->leftJoin('krajs', 'zapytanias.kraj_id', '=', 'krajs.id')
                ->orderBy('krajs.name', $sortDirection)
                ->select('zapytanias.*');
        } elseif ($sortField === 'zakres') {
            $query->leftJoin('zakres', 'zapytanias.zakres_id', '=', 'zakres.id')
                ->orderBy('zakres.name', $sortDirection)
                ->select('zapytanias.*');
        } elseif ($sortField === 'created_at') {
            $query->orderBy('zapytanias.created_at', $sortDirection);
        } else {
            $query->OrderByCreatedAt();
        }

        return Inertia::render('Zapytania/Index', [
            'filters' => Request::all('search', 'trashed', 'field', 'direction'),
            'stats' => $stats,
            'zapytanias' => $query
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($zapytania) => [
                    'id' => $zapytania->id,
                    'id_zapyt' => $zapytania->id_zapyt,
                    'nazwa_projektu' => $zapytania->nazwa_projektu,
                    'client' => $zapytania->client ? $zapytania->client : null,
                    'kwota' => $zapytania->kwota,
                    'kraj' => $zapytania->kraj ? $zapytania->kraj : null,
                    'waluta' => $zapytania->waluta ? $zapytania->waluta : null,
                    'zakres' => $zapytania->zakres ? $zapytania->zakres : null,
                    'user' => $zapytania->user ? $zapytania->user : null,
                    'otrzymal' => $zapytania->otrzymal ? $zapytania->otrzymal : null,
                    'opracowuje' => $zapytania->opracowuje ? $zapytania->opracowuje : null,
                    'deleted_at' => $zapytania->deleted_at,
                    'created_at' => date($zapytania->created_at),
                    'can' => [
                        'edit' => Auth::user()->can('update', $zapytania),
                    ]
                ])
        ]);
    }

    private function generateNextIdZapyt()
    {
        $year = Carbon::now()->format('Y');

        // Pobieramy numer ostatniego zapytania w bieżącym roku (z wyłączeniem transakcji konkurencyjnych),
        // używając LOCK IN SHARE MODE lub FOR UPDATE, jeśli wywoływane w transakcji
        $lastZapytanie = Zapytania::withTrashed()
            ->where('id_zapyt', 'like', "%/$year")
            ->orderByRaw('CAST(SUBSTRING_INDEX(id_zapyt, "/", 1) AS UNSIGNED) DESC')
            ->lockForUpdate()
            ->first();

        if ($lastZapytanie) {
            $parts = explode('/', $lastZapytanie->id_zapyt);
            $nextNumber = (int)$parts[0] + 1;
        } else {
            // Jeśli nie ma w tym roku, sprawdzamy ogólnie ostatni i zwiększamy,
            // lub zaczynamy od 1
            $lastAny = Zapytania::withTrashed()
                ->orderByRaw('CAST(SUBSTRING_INDEX(id_zapyt, "/", 1) AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            if ($lastAny) {
                $parts = explode('/', $lastAny->id_zapyt);
                $nextNumber = (int)$parts[0] + 1;
            } else {
                $nextNumber = 1;
            }
        }

        return $nextNumber . '/' . $year;
    }

    public function create()
    {
        return Inertia::render('Zapytania/Create', [
            'zakres' => Zakres::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'kraj' => Kraj::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name', 'waluta'),
            'waluta' => Waluta::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::orderBy(DB::raw('TRIM(nazwa)'))
                ->get(['id', 'nazwa'])
                ->values(),
            'id_zapyt' => $this->generateNextIdZapyt(), // Tu generujemy orientacyjnie na potrzeby widoku
        ]);
    }
    public function store(ZapytaniaStoreRequest $request)
    {
        $kurs = $this->changeRate($request->waluta_id, $request->kwota);

        $data = new Zapytania();

        DB::transaction(function () use ($request, $kurs, &$data) {
            // Przypisanie nowego identyfikatora tuż przed zapisem w ramach transakcji
            // Używamy lockForUpdate w generowaniu ID, co zapobiega przypisaniu tego samego ID
            $data->id_zapyt = $this->generateNextIdZapyt();

            $data->user_otrzymal_id = $request->user_otrzymal_id;
            $data->data_otrzymania = $request->data_otrzymania;
            $data->data_zlozenia = $request->data_zlozenia;
            $data->client_id = $request->client_id;
            $data->nazwa_projektu = $request->nazwa_projektu;
            $data->preliminarz = $request->preliminarz;
            $data->miejscowosc = $request->miejscowosc;
            $data->kwotaPLN = $kurs[1];
            $data->kurs = $kurs[0];
            $data->kraj_id = $request->kraj_id;
            $data->zakres_id = $request->zakres_id;
            $data->user_opracowuje_id = $request->user_opracowuje_id;
            $data->start = $request->start;
            $data->end = $request->end;
            $data->kwota = $request->kwota;
            $data->waluta_id = $request->waluta_id;
            $data->opis = $request->opis;
            $data->user_id = Auth::id(); // Zmieniono na Auth::id() dla pewności
            $data->save();
        });

        $this->storeActivityLog('Nowe zapytanie', $data->id, $request->client_id, 'zapytania', 'zmiany', Auth::id());

        if (($data->preliminarz ?? null) === 'Tak') {
            $this->sendPreliminarzMail($data->id);
        }

        return Redirect::route('zapytania')->with('success', 'Zapisano.');
    }

    public function edit($id) // Changed type hint from Zapytania $zapytania to $id
    {
        $zapytania = Zapytania::withTrashed()->findOrFail($id); // Manually find the model

        // Fetch the latest offer for this zapytania
        $latestOferta = Oferta::where('zapytania_id', $zapytania->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Zapytania/Edit', [
            'zapytania' => [
                'id' => $zapytania->id,
                'id_zapyt' => $zapytania->id_zapyt,
                'user_otrzymal_id' => $zapytania->user_otrzymal_id,
                'data_otrzymania' => $zapytania->data_otrzymania,
                'data_zlozenia' => $zapytania->data_zlozenia ? $zapytania->data_zlozenia->format('Y-m-d') : null,
                'client_id' => $zapytania->client_id,
                'nazwa_projektu' => $zapytania->nazwa_projektu,
                'preliminarz' => $zapytania->preliminarz,
                'miejscowosc' => $zapytania->miejscowosc,
                'kraj_id' => $zapytania->kraj_id,
                'zakres_id' => $zapytania->zakres_id,
                'user_opracowuje_id' => $zapytania->user_opracowuje_id,
                'start' => $zapytania->start,
                'end' => $zapytania->end,
                'kwota' => $zapytania->kwota,
                'waluta_id' => $zapytania->waluta_id,
                'opis' => $zapytania->opis,
                'wznowienie' => $zapytania->wznowienie,
                'user_id' => $zapytania->user_id,
                'deleted_at' => $zapytania->deleted_at,
                'can' => [
                    'edit' => Auth::user()->can('update', $zapytania),
                ]
            ],
            'branzas' => Branza::get(),
            'krajs' => Kraj::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name', 'waluta'),
            'waluta' => Waluta::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'zakres' => Zakres::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'clients' => Client::orderBy(DB::raw('TRIM(nazwa)'))->get()->map->only('id', 'nazwa'),
            'oferty' => Oferta::with(['user', 'ofertastatus'])->where('zapytania_id', $zapytania->id)->get(),
            'clientById' => Client::where('id', $zapytania->client_id)->withTrashed()->firstOrFail(),
            'archiwumOpis' => ArchiwumZapytania::with('user')->where('zapytania_id', $zapytania->id)->get(),
            'kontakty' => Kontakt::with(['user', 'kontaktperson', 'children.user', 'children.kontaktperson'])
                ->where('zapytania_id', $zapytania->id)
                ->whereNull('parent_id') // Tylko główne wątki
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($kontakt) => [
                    'id' => $kontakt->id,
                    'subject' => $kontakt->subject,
                    'description' => $kontakt->description,
                    'call_date' => $kontakt->call_date ? $kontakt->call_date->format('Y-m-d') : null,
                    'call_time' => $kontakt->call_time,
                    'next_call_date' => $kontakt->next_call_date ? $kontakt->next_call_date->format('Y-m-d') : null,
                    'next_call_time' => $kontakt->next_call_time,
                    'user' => $kontakt->user,
                    'kontaktperson' => $kontakt->kontaktperson,
                    'children' => $kontakt->children->map(fn ($reply) => [
                        'id' => $reply->id,
                        'subject' => $reply->subject,
                        'description' => $reply->description,
                        'call_date' => $reply->call_date ? $reply->call_date->format('Y-m-d') : null,
                        'call_time' => $reply->call_time,
                        'next_call_date' => $reply->next_call_date ? $reply->next_call_date->format('Y-m-d') : null,
                        'next_call_time' => $reply->next_call_time,
                        'user' => $reply->user,
                        'kontaktperson' => $reply->kontaktperson,
                    ]),
                ]),
            'activities' => $zapytania->activities()->with('causer')->latest()->get()->map(fn ($activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'user' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                'changes' => $activity->changes,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ]),
            'wznowienia' => ZapytaniaWznowienie::with(['user', 'zakres', 'waluta', 'opracowuje']) // Eager load new relationships
                ->where('id_zapytania', $zapytania->id)
                ->orderBy('time', 'desc')
                ->get()
                ->map(fn ($wznowienie) => [
                    'id' => $wznowienie->id,
                    'text' => $wznowienie->text,
                    'user' => $wznowienie->user ? $wznowienie->user->only('first_name', 'last_name') : null,
                    'time' => $wznowienie->time->format('Y-m-d H:i:s'),
                    'data_otrzymania' => $wznowienie->data_otrzymania ? $wznowienie->data_otrzymania->format('Y-m-d') : null,
                    'data_zlozenia' => $wznowienie->data_zlozenia ? $wznowienie->data_zlozenia->format('Y-m-d') : null,
                    'preliminarz' => $wznowienie->preliminarz,
                    'zakres' => $wznowienie->zakres ? $wznowienie->zakres->only('name') : null,
                    'opracowuje' => $wznowienie->opracowuje ? $wznowienie->opracowuje->only('first_name', 'last_name') : null,
                    'start' => $wznowienie->start ? $wznowienie->start->format('Y-m-d') : null,
                    'end' => $wznowienie->end ? $wznowienie->end->format('Y-m-d') : null,
                    'kwota' => $wznowienie->kwota,
                    'waluta' => $wznowienie->waluta ? $wznowienie->waluta->only('name') : null,
                    'oferta_created_at' => ($latestOferta && $latestOferta->created_at->greaterThan($wznowienie->time)) ? $latestOferta->created_at->format('Y-m-d H:i:s') : null,
                    'oferta_id' => ($latestOferta && $latestOferta->created_at->greaterThan($wznowienie->time)) ? $latestOferta->id : null,
                ]),
        ]);
    }

    public function createWznowienie(Zapytania $zapytania)
    {
        return Inertia::render('Zapytania/WznowienieCreate', [
            'zapytania' => [
                'id' => $zapytania->id,
                'id_zapyt' => $zapytania->id_zapyt,
                'nazwa_projektu' => $zapytania->nazwa_projektu,
            ],
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'zakres' => Zakres::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'waluta' => Waluta::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
        ]);
    }

    public function editWznowienie(Zapytania $zapytania, ZapytaniaWznowienie $wznowienie)
    {
        return Inertia::render('Zapytania/WznowienieEdit', [
            'zapytania' => [
                'id' => $zapytania->id,
                'id_zapyt' => $zapytania->id_zapyt,
                'nazwa_projektu' => $zapytania->nazwa_projektu,
            ],
            'wznowienie' => [
                'id' => $wznowienie->id,
                'text' => $wznowienie->text,
                'id_zapytania' => $wznowienie->id_zapytania,
                'id_user' => $wznowienie->id_user,
                'data_otrzymania' => $wznowienie->data_otrzymania ? $wznowienie->data_otrzymania->format('Y-m-d') : null,
                'data_zlozenia' => $wznowienie->data_zlozenia ? $wznowienie->data_zlozenia->format('Y-m-d') : null,
                'preliminarz' => $wznowienie->preliminarz,
                'zakres_id' => $wznowienie->zakres_id,
                'user_opracowuje_id' => $wznowienie->user_opracowuje_id,
                'start' => $wznowienie->start ? $wznowienie->start->format('Y-m-d') : null,
                'end' => $wznowienie->end ? $wznowienie->end->format('Y-m-d') : null,
                'kwota' => $wznowienie->kwota,
                'waluta_id' => $wznowienie->waluta_id,
                'created_at' => $wznowienie->time ? $wznowienie->time->format('Y-m-d') : null,
            ],
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'zakres' => Zakres::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'waluta' => Waluta::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
        ]);
    }

    public function updateWznowienie(Zapytania $zapytania, ZapytaniaWznowienie $wznowienie, WznowienieUpdateRequest $request)
    {
        $wznowienie->update($request->validated());

        return Redirect::route('zapytania.edit', $zapytania->id)->with('success', 'Wznowienie zaktualizowane.');
    }

    public function destroyWznowienie(Zapytania $zapytania, ZapytaniaWznowienie $wznowienie)
    {
        $wznowienie->delete();

        return Redirect::route('zapytania.edit', $zapytania->id)->with('success', 'Wznowienie usunięte.');
    }

    public function update(Zapytania $zapytania, ZapytaniaStoreRequest $request)
    {
        $this->authorize('update', $zapytania);

        // Zapobiegamy przypadkowej zmianie id_zapyt podczas update
        $zapytania->update($request->except('id_zapyt'));
        $this->saveRate($zapytania->id, $request->kurs, $request->kwota);
        ($zapytania)??$this->storeActivityLog('Poprawiono zapytanie', $zapytania->id, $request->client_id, 'zapytania', 'zmiany', Auth::id());


        return Redirect::route('zapytania')->with('success', 'Zapytanie poprawione.');
    }

    public function destroy(Zapytania $zapytania)
    {
        $this->authorize('update', $zapytania);

        Oferta::where('zapytania_id', $zapytania->id)->delete();

        $zapytania->delete();

        return Redirect::route('zapytania.edit', $zapytania->id)->with('success', 'Zapytanie zarchiwizowane.');
    }
    public function archiwum(Zapytania $zapytania)
    {
//        $zapytania = Zapytania::where('id', $id)->withTrashed()->get()->map->only('id', 'id_zapyt', 'nazwa_projektu');

        return Inertia::render('Zapytania/Archiwum', [
            'zapytania' => $zapytania,
        ]);
    }
    public function restore(Zapytania $zapytania)
    {
        $this->authorize('update', $zapytania);

        $zapytania->restore();

        return Redirect::back()->with('success', 'Zapytanie przywrócone');
    }
    public function exchangeRate($id)
    {
        $currency = Kursy::select('kurs')->where('waluta_id', $id)->latest()->first();
        return $currency ? (string) $currency->kurs : '1.0';
    }
    public function changeRate($waluta, $kwota)
    {
        $waluta = Waluta::where('id', $waluta)->pluck('id');
        if($waluta->isEmpty()) {
            return [1.0, (float)$kwota];
        }
        $kurs = $this->exchangeRate($waluta[0]);
        $kwotaPLN = (float) $kwota * (float) $kurs;

        return [(float) $kurs, (float) $kwotaPLN];
    }
    public function saveRate($id, $kurs, $kwotaPLN)
    {
        $data = Zapytania::find($id);
        $data->kurs = (float) $kurs;
        $data->kwotaPLN = $kwotaPLN;
        $data->save();
    }
    public function pdf(Zapytania $zapytania)
    {
        $data = Zapytania::with('client')
            ->with('user')
            ->with('kraj')
            ->with('zakres')
            ->get();

        $data = $this->zapytaniaById($zapytania->id);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('zapytaniaPdf', compact('data'));
        return $pdf->stream('zapytanie'.$zapytania->id_zapyt.'.pdf');
    }
    public function zapytaniaById($id)
    {
        $data = Zapytania::with('client')
            ->with('user')
            ->with('kraj')
            ->with('zakres')
            ->where('zapytanias.id', $id)
            ->firstOrFail();

        return $data;
    }
    public function wznowienie(Zapytania $zapytania)
    {
        return Inertia::render('Zapytania/Archiwum', [
            'zapytania' => $zapytania,
        ]);
    }
    public function storeWznowienie(Zapytania $zapytania, WznowienieStoreRequest $request)
    {
        $wznowienie = new ZapytaniaWznowienie();
        $wznowienie->text = $request->text;
        $wznowienie->id_zapytania = $request->id_zapytania;
        $wznowienie->id_user = $request->id_user;
        $wznowienie->data_otrzymania = $request->data_otrzymania;
        $wznowienie->data_zlozenia = $request->data_zlozenia;
        $wznowienie->preliminarz = $request->preliminarz;
        $wznowienie->zakres_id = $request->zakres_id;
        $wznowienie->user_opracowuje_id = $request->user_opracowuje_id;
        $wznowienie->start = $request->start;
        $wznowienie->end = $request->end;
        $wznowienie->kwota = $request->kwota;
        $wznowienie->waluta_id = $request->waluta_id;
        $wznowienie->time = Carbon::now(); // Set current timestamp
        $wznowienie->save();

        $zapytania->wznowienie = 2;
        $zapytania->save();

        // Mail PRELIMINARZ dla wznowienia - bazujemy na fladze preliminarz z wznowienia
        if (($wznowienie->preliminarz ?? null) === 'Tak') {
            $this->sendWznowienieMail($wznowienie->id);
        }

        return Redirect::route('zapytania.edit', $zapytania->id)->with('success', 'Wznowienie dodane.');
    }

    /**
     * Wysyla bogaty mail z danymi WZNOWIENIA do osob z flaga preliminarz_email=1.
     * Dane projektu/klienta brane z parent zapytania, dane terminów/kwot/opracowuje z wznowienia.
     */
    protected function sendWznowienieMail(int $wznowienieId): void
    {
        try {
            $wznowienie = ZapytaniaWznowienie::with([
                'zapytanie.client',
                'zapytanie.kraj',
                'zakres',
                'waluta',
                'opracowuje',
            ])->find($wznowienieId);

            if (!$wznowienie || !$wznowienie->zapytanie) {
                Log::warning('sendWznowienieMail: nie znaleziono wznowienia lub parent zapytania', ['wznowienie_id' => $wznowienieId]);
                return;
            }

            $parent = $wznowienie->zapytanie;

            // Merged data: parent dla danych klient/projekt, wznowienie dla terminow/kwoty/opracowuje
            $data = new \stdClass();
            $data->id_zapyt = $parent->id_zapyt;
            $data->wznowienie = 2; // pokaze badge WZNOWIENIE w blade
            $data->client = $parent->client;
            $data->nazwa_projektu = $parent->nazwa_projektu;
            $data->miejscowosc = $parent->miejscowosc;
            $data->kraj = $parent->kraj;
            $data->zakres = $wznowienie->zakres;
            $data->data_otrzymania = $wznowienie->data_otrzymania ? $wznowienie->data_otrzymania->format('Y-m-d') : null;
            $data->data_zlozenia = $wznowienie->data_zlozenia ? $wznowienie->data_zlozenia->format('Y-m-d') : null;
            $data->opracowuje = $wznowienie->opracowuje;
            $data->start = $wznowienie->start ? $wznowienie->start->format('Y-m-d') : null;
            $data->end = $wznowienie->end ? $wznowienie->end->format('Y-m-d') : null;
            $data->kwota = $wznowienie->kwota;
            $data->waluta = $wznowienie->waluta;
            $data->opis = $wznowienie->text;

            $emails = User::where('preliminarz_email', true)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email');

            if ($emails->isEmpty()) {
                Log::info('sendWznowienieMail: brak userow z flaga preliminarz_email=true', ['wznowienie_id' => $wznowienieId]);
                return;
            }

            Mail::send(new ZapytaniaMail($data, $emails->all()));
            Log::info('sendWznowienieMail: mail wyslany', [
                'wznowienie_id' => $wznowienieId,
                'zapytania_id' => $parent->id,
                'recipients' => $emails->all(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Blad wysylki maila wznowienia: ' . $e->getMessage(), [
                'wznowienie_id' => $wznowienieId,
            ]);
        }
    }

    /**
     * Wysyla bogaty mail z informacjami o zapytaniu do osob z flaga preliminarz_email=1.
     * Caller jest odpowiedzialny za decyzje czy ma sie wyslac (zwykle: preliminarz=Tak).
     */
    protected function sendPreliminarzMail(int $zapytaniaId): void
    {
        try {
            $data = Zapytania::with(['client', 'user', 'kraj', 'zakres', 'opracowuje', 'waluta'])
                ->find($zapytaniaId);

            if (!$data) {
                Log::warning('sendPreliminarzMail: nie znaleziono zapytania', ['zapytania_id' => $zapytaniaId]);
                return;
            }

            $emails = User::where('preliminarz_email', true)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->pluck('email');

            if ($emails->isEmpty()) {
                Log::info('sendPreliminarzMail: brak userow z flaga preliminarz_email=true', ['zapytania_id' => $zapytaniaId]);
                return;
            }

            Mail::send(new ZapytaniaMail($data, $emails->all()));
            Log::info('sendPreliminarzMail: mail wyslany', [
                'zapytania_id' => $zapytaniaId,
                'recipients' => $emails->all(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Blad wysylki maila preliminarz: ' . $e->getMessage(), [
                'zapytania_id' => $zapytaniaId,
            ]);
        }
    }

    public function deleteWznowienie(Zapytania $zapytania)
    {
        $zapytania->wznowienie = 1;
        $zapytania->save();
    }

}
