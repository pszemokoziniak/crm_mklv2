<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use App\Models\User;
use App\Models\Zapytania;
use App\Models\Oferta;
use App\Models\FutureProject;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Zadania;
use Illuminate\Support\Facades\DB; // Dodaj to

class KontaktController extends Controller
{
    public function index()
    {
        return Inertia::render('Kontakt/Index', [
            'filters' => Request::all('search', 'field', 'direction'),
            'kontakty' => Kontakt::with(['user', 'opiekun', 'zapytania', 'oferta', 'futureProject', 'kontaktperson', 'client'])
                ->whereNull('parent_id')
                ->withCount('children')
                ->filter(Request::only('search'))
                ->when(Request::get('field'), function ($query, $field) {
                    $direction = Request::get('direction', 'asc');
                    if ($field === 'client') {
                        $query->join('clients', 'kontakts.client_id', '=', 'clients.id')
                            ->orderBy('clients.nazwa', $direction)
                            ->select('kontakts.*');
                    } elseif ($field === 'user') {
                        $query->join('users', 'kontakts.user_id', '=', 'users.id')
                            ->orderBy('users.last_name', $direction)
                            ->select('kontakts.*');
                    } elseif ($field === 'opiekun') {
                        $query->join('users as opiekunowie', 'kontakts.opiekun_id', '=', 'opiekunowie.id')
                            ->orderBy('opiekunowie.last_name', $direction)
                            ->select('kontakts.*');
                    } else {
                        $query->orderBy($field, $direction);
                    }
                }, function ($query) {
                    $query->orderBy('created_at', 'desc');
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($kontakt) => [
                    'id' => $kontakt->id,
                    'subject' => $kontakt->subject,
                    'contact_type' => $kontakt->contact_type,
                    'call_date' => $kontakt->call_date,
                    'client' => $kontakt->client ? $kontakt->client->nazwa : '-',
                    'user' => $kontakt->user ? $kontakt->user->first_name . ' ' . $kontakt->user->last_name : '-',
                    'opiekun' => $kontakt->opiekun ? $kontakt->opiekun->first_name . ' ' . $kontakt->opiekun->last_name : '-',
                    'zapytanie' => $kontakt->zapytania ? $kontakt->zapytania->nazwa_projektu : '-',
                    'oferta' => $kontakt->oferta ? 'Oferta #' . $kontakt->oferta->id : '-',
                    'future_project' => $kontakt->futureProject ? $kontakt->futureProject->nazwa : '-',
                    'replies_count' => $kontakt->children_count,
                ]),
        ]);
    }

    public function clientIndex($client_id)
    {
        return Inertia::render('Kontakt/ClientIndex', [
            'kontakt' => Kontakt::with('user', 'opiekun', 'zapytania', 'oferta', 'futureProject', 'kontaktperson')
                ->where('client_id', $client_id)
                ->whereNull('parent_id')
                ->withCount('children')
                ->orderBy('call_date', 'desc')
                ->get(),
            'client_id' => $client_id,
            'client' => Client::find($client_id),
        ]);
    }

    public function create(Client $client = null, $kontaktPersonId = null)
    {
        if (!$client && Request::get('client')) {
            $client = Client::find(Request::get('client'));
        }

        $parentId = Request::get('parent_id');
        $parentKontakt = $parentId ? Kontakt::find($parentId) : null;

        if (!$client && $parentKontakt) {
            $client = $parentKontakt->client;
        }

        $zapytaniaId = Request::get('zapytania_id');
        $ofertaId = Request::get('oferta_id');
        $futureProjectId = Request::get('future_project_id');

        return Inertia::render('Kontakt/Create', [
            'clients' => Client::orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
            ]),
            'zapytanias' => $client ? Zapytania::where('client_id', $client->id)->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get() : [],
            'ofertas' => $client ? Oferta::with(['waluta', 'zapytania'])->where('client_id', $client->id)->orderBy('id')->get()->map(fn($o) => [
                'id' => $o->id,
                'label' => ($o->zapytania && $o->zapytania->nazwa_projektu ? $o->zapytania->nazwa_projektu . ' — ' : '') . 'Oferta #' . $o->id . ($o->kwota ? ' (' . $o->kwota . ' ' . ($o->waluta ? $o->waluta->name : '') . ')' : ''),
            ]) : [],
            'futureProjects' => $client ? FutureProject::where('client_id', $client->id)->orderBy(DB::raw('TRIM(nazwa)'))->get() : [],
            'client_id' => $client ? $client->id : null,
            'client' => $client,
            'kontaktPersons' => $client ? KontaktPerson::where('client_id', $client->id)->orderBy(DB::raw('TRIM(first_name)'))->orderBy(DB::raw('TRIM(last_name)'))->get() : [],
            'existingTopics' => $client ? Kontakt::where('client_id', $client->id)->whereNull('parent_id')->orderBy('created_at', 'desc')->get(['id', 'subject', 'contact_type']) : [],
            'selected_kontakt_person_id' => $kontaktPersonId ?: ($parentKontakt ? $parentKontakt->kontakt_person_id : null),
            'parent_id' => $parentId,
            'parent_subject' => $parentKontakt ? $parentKontakt->subject : null,
            'selected_zapytania_id' => $zapytaniaId ?: ($parentKontakt ? $parentKontakt->zapytania_id : null),
            'selected_oferta_id' => $ofertaId ?: ($parentKontakt ? $parentKontakt->oferta_id : null),
            'selected_future_project_id' => $futureProjectId ?: ($parentKontakt ? $parentKontakt->future_project_id : null),
            'selected_opiekun_id' => $parentKontakt ? $parentKontakt->opiekun_id : auth()->id(),
            'parent_contact_type' => $parentKontakt ? $parentKontakt->contact_type : null,
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'contact_type' => 'nullable|string',
            'description' => 'required|string',
            'call_date' => 'nullable|date',
            'call_time' => 'required',
            'next_call_date' => 'nullable|date',
            'next_call_time' => 'nullable',
            'client_id' => 'required|exists:clients,id',
            'kontakt_person_id' => 'nullable|exists:kontakt_persons,id',
            'zapytania_id' => 'nullable|exists:zapytanias,id',
            'oferta_id' => 'nullable|exists:ofertas,id',
            'future_project_id' => 'nullable|exists:future_projects,id',
            'parent_id' => 'nullable|exists:kontakts,id',
            'opiekun_id' => 'nullable|exists:users,id',
        ], [
            'subject.required' => 'Temat rozmowy jest wymagany.',
            'description.required' => 'Szczegóły rozmowy są wymagane.',
            'call_time.required' => 'Godzina kontaktu jest wymagana.',
            'client_id.required' => 'Wybierz klienta.',
            'client_id.exists' => 'Wybrany klient nie istnieje.',
            'call_date.date' => 'Nieprawidłowy format daty kontaktu.',
            'next_call_date.date' => 'Nieprawidłowy format daty następnego kontaktu.',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        if (empty($data['opiekun_id'])) {
            $data['opiekun_id'] = auth()->id();
        }

        $kontakt = Kontakt::create($data);

        // Aktualizuj data_kontakt na ofercie jeśli ustawiono następny kontakt
        if ($kontakt->oferta_id && $kontakt->next_call_date) {
            \App\Models\Oferta::where('id', $kontakt->oferta_id)
                ->update(['data_kontakt' => $kontakt->next_call_date]);

            // Auto-create Zadanie for opiekun
            $client = Client::find($kontakt->client_id);
            $oferta = Oferta::find($kontakt->oferta_id);

            Zadania::create([
                'responsible_person_id' => $kontakt->opiekun_id,
                'user_id' => auth()->id(),
                'deadline' => $kontakt->next_call_date,
                'subject' => 'Kontakt: ' . ($client->nazwa ?? 'Klient') . ' - ' . ($oferta->projekt ?? 'Oferta'),
                'description' => 'Zaplanowany kontakt z klientem.'
                    . "\nData: " . $kontakt->next_call_date->format('Y-m-d')
                    . ($kontakt->next_call_time ? ' ' . $kontakt->next_call_time : '')
                    . "\nTemat kontaktu: " . $kontakt->subject
                    . "\nOferta: /oferta/" . $kontakt->oferta_id . "/edit",
                'status' => 'aktywne',
            ]);
        }

        $targetId = $kontakt->parent_id ?? $kontakt->id;

        // Powrót do standardowej metody Redirect::route()
        if ($kontakt->zapytania_id) {
            return Redirect::route('zapytania.edit', [
                'zapytania' => $kontakt->zapytania_id,
                'remember_kontakt' => $targetId
            ])->with('success', 'Kontakt dodany.');
        }

        if ($kontakt->oferta_id) {
            return Redirect::route('oferta.edit', [
                'oferta' => $kontakt->oferta_id,
                'remember_kontakt' => $targetId
            ])->with('success', 'Kontakt dodany.');
        }

        if ($kontakt->future_project_id) {
            return Redirect::route('futureproject.edit', $kontakt->future_project_id)->with('success', 'Kontakt dodany.');
        }

        // Reply do istniejącego wątku — wracamy do widoku wątku zamiast do listy.
        if ($kontakt->parent_id) {
            return Redirect::to('/kontakt/' . $targetId . '/edit')->with('success', 'Kontakt dodany.');
        }

        return Redirect::route('kontakt')->with('success', 'Kontakt dodany.');
    }

    public function edit(Kontakt $kontakt)
    {
        // Jeśli wejście przez wpis-dziecko, przeładuj na root, żeby zawsze pracować w widoku wątku
        $requestedId = $kontakt->id;
        if ($kontakt->parent_id && $kontakt->parent) {
            $kontakt = $kontakt->parent;
        }

        $formatEntry = fn(Kontakt $k) => [
            'id' => $k->id,
            'subject' => $k->subject,
            'contact_type' => $k->contact_type,
            'description' => $k->description,
            'call_date' => $k->call_date ? $k->call_date->format('Y-m-d') : null,
            'call_time' => $k->call_time,
            'next_call_date' => $k->next_call_date ? $k->next_call_date->format('Y-m-d') : null,
            'next_call_time' => $k->next_call_time,
            'zapytania_id' => $k->zapytania_id,
            'oferta_id' => $k->oferta_id,
            'future_project_id' => $k->future_project_id,
            'kontakt_person_id' => $k->kontakt_person_id,
            'parent_id' => $k->parent_id,
            'client_id' => $k->client_id,
            'opiekun_id' => $k->opiekun_id,
            'user_id' => $k->user_id,
            'user' => $k->user ? ['id' => $k->user->id, 'first_name' => $k->user->first_name, 'last_name' => $k->user->last_name] : null,
            'opiekun' => $k->opiekun ? ['id' => $k->opiekun->id, 'first_name' => $k->opiekun->first_name, 'last_name' => $k->opiekun->last_name] : null,
            'kontaktperson' => $k->kontaktperson ? ['id' => $k->kontaktperson->id, 'first_name' => $k->kontaktperson->first_name, 'last_name' => $k->kontaktperson->last_name] : null,
        ];

        return Inertia::render('Kontakt/Edit', [
            'kontakt' => $formatEntry($kontakt),
            'requestedId' => $requestedId,
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
            ]),
            'replies' => $kontakt->children()->with(['user', 'opiekun', 'kontaktperson'])->get()->map($formatEntry),
            'zapytanias' => Zapytania::where('client_id', $kontakt->client_id)->orderBy(DB::raw('TRIM(nazwa_projektu)'))->get(),
            'ofertas' => Oferta::with(['waluta', 'zapytania'])->where('client_id', $kontakt->client_id)->orderBy('id')->get()->map(fn($o) => [
                'id' => $o->id,
                'label' => ($o->zapytania && $o->zapytania->nazwa_projektu ? $o->zapytania->nazwa_projektu . ' — ' : '') . 'Oferta #' . $o->id . ($o->kwota ? ' (' . $o->kwota . ' ' . ($o->waluta ? $o->waluta->name : '') . ')' : ''),
            ]),
            'futureProjects' => FutureProject::where('client_id', $kontakt->client_id)->orderBy(DB::raw('TRIM(nazwa)'))->get(),
            'kontaktPersons' => KontaktPerson::where('client_id', $kontakt->client_id)->orderBy(DB::raw('TRIM(first_name)'))->orderBy(DB::raw('TRIM(last_name)'))->get(),
            'client' => Client::find($kontakt->client_id),
            'clients' => Client::orderBy(DB::raw('TRIM(nazwa)'))->get(),
        ]);
    }

    public function update(Kontakt $kontakt, \Illuminate\Http\Request $request)
    {
        $kontakt->update($request->all());

        // Check if the updated contact is associated with a zapytania
        if ($kontakt->zapytania_id) {
            return Redirect::route('zapytania.edit', [
                'zapytania' => $kontakt->zapytania_id,
                'remember_kontakt' => $kontakt->id // Use the current kontakt's ID
            ])->with('success', 'Poprawiono kontakt.');
        }

        // If not associated with zapytania, redirect back as before
        return Redirect::back()->with('success', 'Poprawiono kontakt.');
    }

    public function destroy(Kontakt $kontakt)
    {
        $kontakt->delete();
        return Redirect::route('kontakt')->with('success', 'Usunięte.');
    }

    public function restore(Kontakt $kontakt)
    {
        $kontakt->restore();
        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
