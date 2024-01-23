<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use App\Models\Oferta;
use App\Models\Zapytania;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KontaktController extends Controller
{
    public function index($client_id)
    {
        return Inertia::render('Kontakt/Index', [
            'kontakt' => Kontakt::with('user')
                ->with('zapytania')
                ->with('oferta')
                ->with('kontaktperson')
                ->where('client_id', $client_id)
                ->get(),
            'client_id' => $client_id,
        ]);
    }
        public function create(Client $client, $kontaktPerson)
    {
        return Inertia::render('Kontakt/Create', [
//            'clients' => Client::get(),
            'zapytanias' => Zapytania::withTrashed()->get(),
//            'ofertas' => Oferta::get(),
            'client_id' => $client->id,
            'kontaktPersons' => KontaktPerson::where('client_id', $client->id)->get(),
            'kontaktPerson' => KontaktPerson::where('id', $kontaktPerson)->firstOrFail(),
        ]);
    }
    public function store(Request $request)
    {
        Kontakt::create($request->all());

        return redirect()->route('kontakt', [$request->client_id])->with('success', 'Kontakt dodana.');
    }
    public function edit(Kontakt $kontakt)
    {
        return Inertia::render('Kontakt/Edit', [
            'kontakt' => [
                'id' => $kontakt->id,
                'subject' => $kontakt->subject,
                'description' => $kontakt->description,
                'call_time' => $kontakt->call_time,
                'zapytania_id' => $kontakt->zapytania_id,
                'deleted_at' => $kontakt->deleted_at,
            ],
            'zapytanias' => Zapytania::get()->map->only('id', 'nazwa_projektu'),
            'client_id' => $kontakt->client_id,
        ]);
    }

    public function update(Kontakt $kontakt, Request $request)
    {
        $kontakt->update($request->all());

        return Redirect::route('kontakt', $kontakt->client_id)->with('success', 'Poprawione.');
    }

    public function destroy(Kontakt $kontakt)
    {
        $kontakt->delete();

        return Redirect::route('kontakt', $kontakt->client_id)->with('success', 'Usunięte.');
    }
}
