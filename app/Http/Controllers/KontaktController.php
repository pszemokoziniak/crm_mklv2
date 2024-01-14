<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use App\Models\Oferta;
use App\Models\Zapytania;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KontaktController extends Controller
{
    public function index($client_id)
    {
        dd(Kontakt::with('user')
            ->with('zapytania')
            ->with('kontaktperson')
            ->where('client_id', $client_id)
            ->get());
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
            'zapytanias' => Zapytania::get(),
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
}
