<?php

namespace App\Http\Controllers;

use App\Http\Requests\FutureProjectRequest;
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Faza;
use App\Models\FutureProject;
use App\Models\Kraj;
use App\Models\Objekt;
use App\Models\User;
use App\Models\Zakres;
use App\Models\Zapytania;
use Carbon\Carbon;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FutureProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('FutureProjects/Index', [
            'filters' => Request::all('search', 'trashed'),
            'futureprojects' => FutureProject::with('client')
                ->with('user')
                ->with('kraj')
                ->with('faza')
                ->with('objekt')
                ->OrderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($futureproject) => [
                    'id' => $futureproject->id,
                    'nazwa' => $futureproject->nazwa,
                    'miasto' => $futureproject->miasto,
                    'start' => $futureproject->start,
                    'end' => $futureproject->end,
                    'client' => $futureproject->client ? $futureproject->client : null,
                    'kraj' => $futureproject->kraj ? $futureproject->kraj : null,
                    'faza' => $futureproject->faza ? $futureproject->faza : null,
                    'objekt' => $futureproject->objekt ? $futureproject->objekt : null,
                    'user' => $futureproject->user ? $futureproject->user : null,
                    'kwota' => $futureproject->kwota,

                    'deleted_at' => $futureproject->deleted_at,
                    'created_at' => date($futureproject->created_at)
                ])
        ]);
    }

    public function create()
    {

        return Inertia::render('FutureProjects/Create', [
            'objekt' => Objekt::get()->map->only('id', 'name'),
            'faza' => Faza::get()->map->only('id', 'name'),
            'kraj' => Kraj::get()->map->only('id', 'name', 'waluta'),
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::get()->map->only('id', 'nazwa'),
        ]);
    }
    public function store(FutureProjectRequest $request)
    {
        FutureProject::create($request->all());
//        $walutaName = Kraj::where('id', $request->waluta)->pluck('waluta');
//        (int) $kwotaPLN = (int) $request->kwota * (float) $this->exchangeRate($walutaName[0]);
//        (float) $kurs = $this->exchangeRate($walutaName[0]);
//        $data = new Zapytania();
//        $data->id_zapyt = $request->id_zapyt;
//        $data->user_otrzymal_id = $request->user_otrzymal_id;
//        $data->data_otrzymania = $request->data_otrzymania;
//        $data->data_zlozenia = $request->data_zlozenia;
//        $data->client_id = $request->client_id;
//        $data->nazwa_projektu = $request->nazwa_projektu;
//        $data->miejscowosc = $request->miejscowosc;
//        $data->kwotaPLN = $kwotaPLN;
//        $data->kurs = $kurs;
//        $data->kraj_id = $request->kraj_id;
//        $data->zakres_id = $request->zakres_id;
//        $data->user_opracowuje_id = $request->user_opracowuje_id;
//        $data->start = $request->start;
//        $data->end = $request->end;
//        $data->kwota = $request->kwota;
//        $data->waluta = $request->waluta;
//        $data->opis = $request->opis;
//        $data->user_id = $request->user_id;
//        $data->save();
        return Redirect::route('futureproject')->with('success', 'Zapisano.');
    }

    public function edit(FutureProject $futureProject)
    {
        return Inertia::render('FutureProjects/Edit', [
            'futureproject' => [
                'id' => $futureProject->id,
                'nazwa' => $futureProject->nazwa,
                'miasto' => $futureProject->miasto,
                'kraj_id' => $futureProject->kraj_id,
                'objekt_id' => $futureProject->objekt_id,
                'client_id' => $futureProject->client_id,
                'start' => $futureProject->start,
                'end' => $futureProject->end,
                'opis' => $futureProject->opis,
                'inwestor' => $futureProject->inwestor,
                'dane_kontaktowe' => $futureProject->dane_kontaktowe,
                'data_kontakt' => $futureProject->data_kontakt,
                'faza_id' => $futureProject->faza_id,
                'user_id' => $futureProject->user_id,
                'deleted_at' => $futureProject->deleted_at,
            ],
            'objekt' => Objekt::get()->map->only('id', 'name'),
            'faza' => Faza::get()->map->only('id', 'name'),
            'krajs' => Kraj::get()->map->only('id', 'name'),
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::get()->map->only('id', 'nazwa'),
        ]);
    }

    public function update(FutureProject $futureProject, FutureProjectRequest $request)
    {
        $futureProject->update($request->all());

        return Redirect::back()->with('success', 'Zapytanie poprawione.');
    }

    public function destroy(FutureProject $futureProject)
    {
        $futureProject->delete();

        return Redirect::back()->with('success', 'Zapytanie usunięte.');
    }

    public function restore(FutureProject $futureProject)
    {
        $futureProject->restore();

        return Redirect::back()->with('success', 'Zapytanie przywrócone');
    }
}
