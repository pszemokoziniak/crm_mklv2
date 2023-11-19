<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfertaStoreRequest;
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\Oferta;
use App\Models\OfertaStatus;
use App\Models\User;
use App\Models\Zakres;
use App\Models\Zapytania;
use Carbon\Carbon;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OfertaController extends Controller
{
    public function index()
    {
        return Inertia::render('Zapytania/Index', [
            'filters' => Request::all('search', 'trashed'),
            'zapytanias' => Zapytania::with('client')
                ->with('user')
                ->with('kraj')
                ->with('zakres')
                ->OrderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($zapytania) => [
                    'id' => $zapytania->id,
                    'id_zapyt' => $zapytania->id_zapyt,
                    'nazwa_projektu' => $zapytania->nazwa_projektu,
                    'client' => $zapytania->client ? $zapytania->client : null,
                    'kwota' => $zapytania->kwota,
                    'kraj' => $zapytania->kraj ? $zapytania->kraj : null,
                    'zakres' => $zapytania->zakres ? $zapytania->zakres : null,
                    'user' => $zapytania->user ? $zapytania->user : null,
                    'deleted_at' => $zapytania->deleted_at,
                    'created_at' => date($zapytania->created_at)
                ])
        ]);
    }

    public function create()
    {

        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::get()->map->only('id', 'nazwa_projektu'),
            'typs' => ['Klient oferuje', 'Klient na kontrakt'],
            'clients' => Client::get()->map->only('id', 'nazwa'),
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
            'statuses' => OfertaStatus::get()->map->only('id', 'name'),
            'krajs' => Kraj::get()->map->only('id', 'waluta'),
        ]);
    }
    public function store(OfertaStoreRequest $request)
    {
        Oferta::create($request->all());

        return Redirect::route('oferta')->with('success', 'Zapisano.');
    }

    public function edit(Zapytania $zapytania)
    {
        return Inertia::render('Zapytania/Edit', [
            'zapytania' => [
                'id' => $zapytania->id,
                'id_zapyt' => $zapytania->id_zapyt,
                'user_otrzymal_id' => $zapytania->user_otrzymal_id,
                'data_otrzymania' => $zapytania->data_otrzymania,
                'data_zlozenia' => $zapytania->data_zlozenia,
                'client_id' => $zapytania->client_id,
                'nazwa_projektu' => $zapytania->nazwa_projektu,
                'miejscowosc' => $zapytania->miejscowosc,
                'kraj_id' => $zapytania->kraj_id,
                'zakres_id' => $zapytania->zakres_id,
                'user_opracowuje_id' => $zapytania->user_opracowuje_id,
                'start' => $zapytania->start,
                'end' => $zapytania->end,
                'kwota' => $zapytania->kwota,
                'waluta' => $zapytania->waluta,
                'opis' => $zapytania->opis,
                'user_id' => $zapytania->user_id,
                'deleted_at' => $zapytania->deleted_at,
            ],
            'branzas' => Branza::get(),
            'krajs' => Kraj::get(),
            'users' => User::get(),
            'zakres' => Zakres::get(),
            'clients' => Client::get(),
        ]);
    }

    public function update(Zapytania $zapytania, ZapytaniaStoreRequest $request)
    {
        $zapytania->update($request->all());

        return Redirect::back()->with('success', 'Zapytanie poprawione.');
    }

    public function destroy(Zapytania $zapytania)
    {
        $zapytania->delete();

        return Redirect::back()->with('success', 'Zapytanie usunięte.');
    }

    public function restore(Zapytania $zapytania)
    {
        $zapytania->restore();

        return Redirect::back()->with('success', 'Zapytanie przywrócone');
    }
}
