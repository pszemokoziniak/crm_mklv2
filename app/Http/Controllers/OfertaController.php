<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfertaStoreRequest;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\Kursy;
use App\Models\Oferta;
use App\Models\OfertaStatus;
use App\Models\User;
use App\Models\Waluta;
use App\Models\Zakres;
use App\Models\Zapytania;
use App\Models\Branza;
use App\Models\Kontakt;
use App\Models\ZapytaniaWznowienie as Wznowienie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfertaController extends Controller
{
    use StoreActivityLog;

    public function index()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $statusCounts = Oferta::select('oferta_status_id', DB::raw('count(*) as cnt'))
            ->groupBy('oferta_status_id')
            ->pluck('cnt', 'oferta_status_id');

        $statuses = OfertaStatus::pluck('name', 'id');

        $statusStats = [];
        foreach ($statuses as $id => $name) {
            $statusStats[] = [
                'name' => $name,
                'count' => $statusCounts[$id] ?? 0,
            ];
        }

        $stats = [
            'total' => Oferta::count(),
            'this_month' => Oferta::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])->count(),
            'prev_month' => Oferta::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])->count(),
            'statuses' => $statusStats,
        ];

        return Inertia::render('Oferta/Index', [
            'filters' => Request::all('search', 'trashed'),
            'stats' => $stats,
            'statusOptions' => OfertaStatus::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'ofertas' => Oferta::with(['client', 'user', 'zapytania', 'status', 'waluta'])
                ->OrderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($oferta) => [
                    'id' => $oferta->id,
                    'typ' => $oferta->typ,
                    'client' => $oferta->client,
                    'zapytania' => $oferta->zapytania,
                    'kwota' => $oferta->kwota,
                    'kwotaPLN' => $oferta->kwotaPLN,
                    'waluta' => $oferta->waluta,
                    'status' => $oferta->status,
                    'user' => $oferta->user,
                    'deleted_at' => $oferta->deleted_at,
                    'created_at' => $oferta->created_at->format('Y-m-d')
                ])
        ]);
    }

    public function create()
    {
        $wznowienieId = Request::input('wznowienie_id');
        $zapytaniaId = Request::input('zapytania_id');

        $props = [
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get(),
            'clients' => Client::select('id', 'nazwa')->orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'users' => User::select('id', 'first_name', 'last_name')->orderBy('first_name')->orderBy('last_name')->get(),
            'statuses' => OfertaStatus::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'waluta' => Waluta::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'wznowienieInfo' => null,
        ];

        if ($wznowienieId && $zapytaniaId) {
            $wznowienie = Wznowienie::find($wznowienieId);
            $zapytania = Zapytania::find($zapytaniaId);

            if ($wznowienie && $zapytania) {
                $oferta = $this->checkStatusOpen($zapytania->id);

                if ($oferta !== null) {
                    return Redirect::route('oferta.edit', $oferta->id)->with('error', 'Nie można dodać nowej oferty, ponieważ do tego zapytania jest otwarta oferta ze statusem => Toczy się.');
                }

                $props['wznowienieInfo'] = [
                    'id' => $wznowienie->id,
                    'zapytania_nazwa' => $zapytania->nazwa_projektu,
                    'zapytania_id' => $zapytania->id,
                ];
                $props['zapytaniaById'] = $zapytania->id;
                $props['clientById'] = $zapytania->client_id;
                $props['prefillData'] = [
                    'opis' => $wznowienie->text,
                    'kwota' => $wznowienie->kwota,
                    'waluta_id' => $wznowienie->waluta_id,
                    'data_wyslania' => optional($wznowienie->data_zlozenia)->format('Y-m-d'),
                ];
            }
        }

        return Inertia::render('Oferta/Create', $props);
    }

    public function createFromWznowienie()
    {
        $wznowienieId = Request::input('wznowienie_id');
        $zapytaniaId = Request::input('zapytania_id');

        $wznowienie = Wznowienie::find($wznowienieId);
        $zapytania = Zapytania::find($zapytaniaId);

        if (!$wznowienie || !$zapytania) {
            return Redirect::back()->with('error', 'Nie znaleziono wznowienia lub zapytania.');
        }

        $oferta = $this->checkStatusOpen($zapytania->id);

        if ($oferta !== null) {
            return Redirect::route('oferta.edit', $oferta->id)->with('error', 'Nie można dodać nowej oferty, ponieważ do tego zapytania jest otwarta oferta ze statusem => Toczy się.');
        }

        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get(),
            'clients' => Client::select('id', 'nazwa')->orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'users' => User::select('id', 'first_name', 'last_name')->orderBy('first_name')->orderBy('last_name')->get(), // Added this line
            'statuses' => OfertaStatus::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'waluta' => Waluta::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'zapytaniaById' => $zapytania->id,
            'clientById' => $zapytania->client_id, // Assuming zapytania has client_id
            'prefillData' => [
                'opis' => $wznowienie->text,
                'kwota' => $wznowienie->kwota,
                'waluta_id' => $wznowienie->waluta_id,
                'data_wyslania' => $wznowienie->data_zlozenia, // Assuming data_zlozenia from wznowienie can be data_wyslania for oferta
            ],
        ]);
    }

    public function createData(Zapytania $zapytania, Client $client)
    {
        $oferta = $this->checkStatusOpen($zapytania->id);

        if ($oferta !== null) {
            return Redirect::route('oferta.edit', $oferta->id)->with('error', 'Nie można dodać nowej oferty, ponieważ do tego zapytania jest otwarta oferta ze statusem => Toczy się.');
        }

        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get(),
            'clients' => Client::select('id', 'nazwa')->orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'statuses' => OfertaStatus::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'waluta' => Waluta::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'zapytaniaById' => $zapytania->id,
            'clientById' => $client->id,
        ]);
    }

    public function store(OfertaStoreRequest $request)
    {
        $kursData = $this->changeRate($request->waluta_id, $request->kwota);

        $oferta = Oferta::create(array_merge($request->validated(), [
            'kurs' => $kursData[0],
            'kwotaPLN' => $kursData[1],
            'user_id' => Auth::id(),
        ]));

        $this->storeActivityLog('Nowa oferta', $oferta->id, $request->client_id, 'oferta', 'zmiany', Auth::id());

        Zapytania::where('id', $request->zapytania_id)->update(['wznowienie' => 1]);

        $redirect = Redirect::route('oferta')->with('success', 'Zapisano ofertę.');

        if ($kursData[2] === false) {
            $redirect->with('error', 'Uwaga: Nie znaleziono aktualnego kursu waluty w bazie. Użyto przelicznika 1.0.');
        }

        return $redirect;
    }

    public function edit(Oferta $oferta)
    {
        $sortedZapytania = Zapytania::select('id', 'nazwa_projektu')->withTrashed()->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get();

        return Inertia::render('Oferta/Edit', [
            'oferta' => [
                'id' => $oferta->id,
                'zapytania_id' => $oferta->zapytania_id,
                'typ' => $oferta->typ,
                'client_id' => $oferta->client_id,
                'data_wyslania' => $oferta->data_wyslania ? $oferta->data_wyslania->format('Y-m-d') : null, // Explicitly format
                'kwota' => $oferta->kwota,
                'waluta_id' => $oferta->waluta_id,
                'data_kontakt' => $oferta->data_kontakt ? $oferta->data_kontakt->format('Y-m-d') : null, // Explicitly format
                'oferta_status_id' => $oferta->oferta_status_id,
                'opis' => $oferta->opis,
                'user_id' => $oferta->user_id,
                'deleted_at' => $oferta->deleted_at,
            ],
            'clients' => Client::select('id', 'nazwa')->orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'zapytanie' => $sortedZapytania,
            'clientById' => Client::select('id', 'nazwa')->where('id', $oferta->client_id)->withTrashed()->first(),
            'zapytaniaById' => Zapytania::select('id', 'nazwa_projektu')->where('id', $oferta->zapytania_id)->withTrashed()->first(),
            'statuses' => OfertaStatus::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'waluta' => Waluta::select('id', 'name')->orderBy(DB::raw('TRIM(name)'))->get(),
            'kontakty' => Kontakt::with(['user', 'kontaktperson', 'children.user', 'children.kontaktperson'])
                ->where('oferta_id', $oferta->id)
                ->whereNull('parent_id')
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
            'activities' => $oferta->activities()->with('causer')->latest()->get()->map(fn ($activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'user' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                'changes' => $activity->changes,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function update(Oferta $oferta, OfertaStoreRequest $request)
    {
        $kursData = $this->changeRate($request->waluta_id, $request->kwota);

        $oferta->update(array_merge($request->validated(), [
            'kurs' => $kursData[0],
            'kwotaPLN' => $kursData[1],
        ]));

        $this->storeActivityLog('Poprawiono ofertę', $oferta->id, $request->client_id, 'oferta', 'zmiany', Auth::id());

        $redirect = Redirect::route('oferta')->with('success', 'Oferta poprawiona.');

        if ($kursData[2] === false) {
            $redirect->with('error', 'Uwaga: Nie znaleziono aktualnego kursu waluty w bazie. Użyto przelicznika 1.0.');
        }

        return $redirect;
    }

    public function updateStatus(Oferta $oferta)
    {
        $data = Request::validate([
            'oferta_status_id' => 'required|exists:oferta_statuses,id',
        ]);

        $oferta->update($data);

        $this->storeActivityLog('Zmiana statusu oferty', $oferta->id, $oferta->client_id, 'oferta', 'zmiany', Auth::id());

        return Redirect::back()->with('success', 'Status oferty zmieniony.');
    }

    public function destroy(Oferta $oferta)
    {
        $oferta->delete();
        return Redirect::back()->with('success', 'Oferta zarchiwizowana.');
    }

    public function restore(Oferta $oferta)
    {
        $oferta->restore();
        return Redirect::back()->with('success', 'Oferta przywrócona');
    }

    public function exchangeRate($walutaId)
    {
        $kurs = Kursy::where('waluta_id', $walutaId)->latest()->first();
        if ($kurs) {
            return [(float) $kurs->kurs, true];
        }
        return [1.0, false];
    }

    public function changeRate($walutaId, $kwota)
    {
        $waluta = Waluta::find($walutaId);

        // Jeśli waluta to PLN, nie pokazujemy błędu o braku kursu
        if ($waluta && $waluta->name === 'PLN') {
            return [1.0, (float) $kwota, true];
        }

        [$kurs, $found] = $this->exchangeRate($walutaId);
        $kwotaPLN = (float) $kwota * $kurs;

        return [$kurs, $kwotaPLN, $found];
    }

    public function checkStatusOpen($id)
    {
        return Oferta::where('zapytania_id', $id)
            ->whereHas('status', function ($query) {
                $query->where('name', 'like', 'Toczy się');
            })->first();
    }
}
