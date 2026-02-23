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

class KontaktController extends Controller
{
    public function index()
    {
        return Inertia::render('Kontakt/Index', [
            'filters' => Request::all('search'),
            'kontakty' => Kontakt::with(['user', 'opiekun', 'zapytania', 'oferta', 'futureProject', 'kontaktperson', 'client'])
                ->whereNull('parent_id')
                ->withCount('children')
                ->orderBy('created_at', 'desc')
                ->filter(Request::only('search'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($kontakt) => [
                    'id' => $kontakt->id,
                    'subject' => $kontakt->subject,
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
            'clients' => Client::orderBy('nazwa')->get(),
            'users' => User::orderBy('last_name')->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
            ]),
            'zapytanias' => $client ? Zapytania::where('client_id', $client->id)->get() : [],
            'ofertas' => $client ? Oferta::where('client_id', $client->id)->get()->map(fn($o) => [
                'id' => $o->id,
                'label' => 'Oferta #' . $o->id . ' (' . $o->kwota . ' ' . ($o->waluta ? $o->waluta->name : '') . ')',
            ]) : [],
            'futureProjects' => $client ? FutureProject::where('client_id', $client->id)->get() : [],
            'client_id' => $client ? $client->id : null,
            'client' => $client,
            'kontaktPersons' => $client ? KontaktPerson::where('client_id', $client->id)->get() : [],
            'existingTopics' => $client ? Kontakt::where('client_id', $client->id)->whereNull('parent_id')->orderBy('created_at', 'desc')->get(['id', 'subject']) : [],
            'selected_kontakt_person_id' => $kontaktPersonId ?: ($parentKontakt ? $parentKontakt->kontakt_person_id : null),
            'parent_id' => $parentId,
            'parent_subject' => $parentKontakt ? $parentKontakt->subject : null,
            'selected_zapytania_id' => $zapytaniaId ?: ($parentKontakt ? $parentKontakt->zapytania_id : null),
            'selected_oferta_id' => $ofertaId ?: ($parentKontakt ? $parentKontakt->oferta_id : null),
            'selected_future_project_id' => $futureProjectId ?: ($parentKontakt ? $parentKontakt->future_project_id : null),
            'selected_opiekun_id' => $parentKontakt ? $parentKontakt->opiekun_id : auth()->id(),
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
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
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        if (empty($data['opiekun_id'])) {
            $data['opiekun_id'] = auth()->id();
        }

        Kontakt::create($data);

        if ($request->oferta_id) {
            return redirect()->route('oferta.edit', $request->oferta_id)->with('success', 'Kontakt dodany.');
        }

        if ($request->zapytania_id) {
            return redirect()->route('zapytania.edit', $request->zapytania_id)->with('success', 'Kontakt dodany.');
        }

        if ($request->future_project_id) {
            return redirect()->route('futureproject.edit', $request->future_project_id)->with('success', 'Kontakt dodany.');
        }

        return redirect()->route('kontakt')->with('success', 'Kontakt dodany.');
    }

    public function edit(Kontakt $kontakt)
    {
        return Inertia::render('Kontakt/Edit', [
            'kontakt' => [
                'id' => $kontakt->id,
                'subject' => $kontakt->subject,
                'description' => $kontakt->description,
                'call_date' => $kontakt->call_date,
                'call_time' => $kontakt->call_time,
                'next_call_date' => $kontakt->next_call_date,
                'next_call_time' => $kontakt->next_call_time,
                'zapytania_id' => $kontakt->zapytania_id,
                'oferta_id' => $kontakt->oferta_id,
                'future_project_id' => $kontakt->future_project_id,
                'kontakt_person_id' => $kontakt->kontakt_person_id,
                'parent_id' => $kontakt->parent_id,
                'client_id' => $kontakt->client_id,
                'opiekun_id' => $kontakt->opiekun_id,
            ],
            'users' => User::orderBy('last_name')->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->first_name . ' ' . $u->last_name,
            ]),
            'replies' => $kontakt->children()->with(['user', 'opiekun', 'kontaktperson'])->get(),
            'zapytanias' => Zapytania::where('client_id', $kontakt->client_id)->get(),
            'ofertas' => Oferta::where('client_id', $kontakt->client_id)->get()->map(fn($o) => [
                'id' => $o->id,
                'label' => 'Oferta #' . $o->id . ' (' . $o->kwota . ' ' . ($o->waluta ? $o->waluta->name : '') . ')',
            ]),
            'futureProjects' => FutureProject::where('client_id', $kontakt->client_id)->get(),
            'kontaktPersons' => KontaktPerson::where('client_id', $kontakt->client_id)->get(),
            'client' => Client::find($kontakt->client_id),
            'clients' => Client::orderBy('nazwa')->get(),
        ]);
    }

    public function update(Kontakt $kontakt, \Illuminate\Http\Request $request)
    {
        $kontakt->update($request->all());
        return Redirect::back()->with('success', 'Poprawione.');
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
