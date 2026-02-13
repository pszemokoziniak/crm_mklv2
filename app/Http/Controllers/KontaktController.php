<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use App\Models\Zapytania;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KontaktController extends Controller
{
    public function index($client_id)
    {
        return Inertia::render('Kontakt/Index', [
            'kontakt' => Kontakt::with('user', 'zapytania', 'oferta', 'kontaktperson')
                ->where('client_id', $client_id)
                ->orderBy('call_date', 'desc')
                ->get(),
            'client_id' => $client_id,
            'client' => Client::find($client_id),
        ]);
    }

    public function create(Client $client, $kontaktPersonId = null)
    {
        return Inertia::render('Kontakt/Create', [
            'zapytanias' => Zapytania::where('client_id', $client->id)->get(),
            'client_id' => $client->id,
            'kontaktPersons' => KontaktPerson::where('client_id', $client->id)->get(),
            'selected_kontakt_person_id' => $kontaktPersonId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'call_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        Kontakt::create($data);

        return redirect()->route('kontakt', [$request->client_id])->with('success', 'Kontakt dodany.');
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
                'zapytania_id' => $kontakt->zapytania_id,
                'kontakt_person_id' => $kontakt->kontakt_person_id,
            ],
            'zapytanias' => Zapytania::where('client_id', $kontakt->client_id)->get(),
            'kontaktPersons' => KontaktPerson::where('client_id', $kontakt->client_id)->get(),
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

    public function restore(Kontakt $kontakt)
    {
        $kontakt->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
