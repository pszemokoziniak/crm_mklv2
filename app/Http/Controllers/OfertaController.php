<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfertaStoreRequest;
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\Kursy;
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
        return Inertia::render('Oferta/Index', [
            'filters' => Request::all('search', 'trashed'),
            'ofertas' => Oferta::with('client')
                ->with('user')
                ->with('kraj')
                ->with('zapytania')
                ->with('status')
                ->OrderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($oferta) => [
                    'id' => $oferta->id,
                    'typ' => $oferta->typ,
                    'client' => $oferta->client ? $oferta->client : null,
                    'zapytania' => $oferta->zapytania ? $oferta->zapytania : null,
                    'kwota' => $oferta->kwota,
                    'waluta' => $oferta->waluta,
                    'kraj' => Kraj::where('id', $oferta->zapytania->kraj_id)->first(),
                    'status' => $oferta->status ? $oferta->status : null,
                    'user' => $oferta->user ? $oferta->user : null,
                    'deleted_at' => $oferta->deleted_at,
                    'created_at' => date($oferta->created_at)
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
        (int) $kwotaPLN = (int) $request->kwota * (float) $this->exchangeRate($request->waluta);
        (float) $kurs = $this->exchangeRate($request->waluta);
            $data = new Oferta();
            $data->zapytania_id = $request->zapytania_id;
            $data->typ = $request->typ;
            $data->client_id = $request->client_id;
            $data->data_wyslania = $request->data_wyslania;
            $data->kwota = $request->kwota;
            $data->waluta = $request->waluta;
            $data->kurs = $kurs;
            $data->kwotaPLN = $kwotaPLN;
            $data->data_kontakt = $request->data_kontakt;
            $data->oferta_status_id = $request->oferta_status_id;
            $data->opis = $request->opis;
            $data->user_id = $request->user_id;
            $data->save();

        return Redirect::route('oferta')->with('success', 'Zapisano.');
    }

    public function edit(Oferta $oferta)
    {
        return Inertia::render('Oferta/Edit', [
            'oferta' => [
                'id' => $oferta->id,
                'zapytania_id' => $oferta->zapytania_id,
                'typ' => $oferta->typ,
                'client_id' => $oferta->client_id,
                'data_wyslania' => $oferta->data_wyslania,
                'kwota' => $oferta->kwota,
                'waluta' => $oferta->waluta,
                'data_kontakt' => $oferta->data_kontakt,
                'oferta_status_id' => $oferta->oferta_status_id,
                'opis' => $oferta->opis,
                'user_id' => $oferta->user_id,
                'deleted_at' => $oferta->deleted_at,
            ],
            'branzas' => Branza::get(),
            'krajs' => Kraj::get(),
            'users' => User::get(),
            'zakres' => Zakres::get(),
            'clients' => Client::get(),
            'zapytanie' => Zapytania::get()->map->only('id', 'nazwa_projektu'),
            'statuses' => OfertaStatus::get()->map->only('id', 'name'),

        ]);
    }

    public function update(Oferta $oferta, OfertaStoreRequest $request)
    {
        $oferta->update($request->all());

        return Redirect::back()->with('success', 'Oferta poprawiona.');
    }

    public function destroy(Oferta $oferta)
    {
        $oferta->delete();

        return Redirect::back()->with('success', 'Oferta usunięta.');
    }

    public function restore(Oferta $oferta)
    {
        $oferta->restore();

        return Redirect::back()->with('success', 'Oferta przywrócona');
    }
//    public function exchangeRate($amount, $currency)
//    {
//        $currency = Kursy::select('kurs')->where('name', $currency)->latest()->first()->toArray();
//        $kwotaPLN = ($amount * $currency['kurs']);
//
//        return (float) $kwotaPLN;
//    }
    public function exchangeRate($currency)
    {
        $currency = Kursy::select('kurs')->where('name', $currency)->latest()->first()->toArray();
        $currency = $currency['kurs'];

        return (string) $currency;
    }
    public function kwotaPLN($amount, $currency)
    {
        $currency = Kursy::select('kurs')->where('name', $currency)->latest()->first()->toArray();
        $kwotaPLN = ($amount * $currency['kurs']);

        return (float) $kwotaPLN;
    }
}
