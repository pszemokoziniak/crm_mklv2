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
use App\Models\Waluta;
use App\Models\Zakres;
use App\Models\Zapytania;
use Carbon\Carbon;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;

class OfertaController extends Controller
{
    use StoreActivityLog;
    public function index()
    {
        return Inertia::render('Oferta/Index', [
            'filters' => Request::all('search', 'trashed'),
            'ofertas' => Oferta::with('client')
                ->with('user')
                ->with('kraj')
                ->with('zapytania')
                ->with('status')
                ->with('waluta')
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
                    'waluta' => $oferta->waluta ? $oferta->waluta : null,
//                    'kraj' => Kraj::where('id', $oferta->zapytania->kraj_id)->firstOrFail(),
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
            'waluta' => Waluta::get()->map->only('id', 'name'),
        ]);
    }
    public function createData(Zapytania $zapytania, Client $client)
    {
//        $this->checkStatusOpen($zapytania->zapytania_id);
        $oferta =  Oferta::with('status')
            ->where('zapytania_id', $zapytania->id)
            ->whereHas('status', function ($query) {
                $query->where('name', 'like', 'Przegrana');
                $query->orWhere('name', 'like', 'Wygrana');
            })->first();

        if ($oferta !== null) {
            return Redirect::route('oferta.edit', $oferta->id)->with('error', 'Nie można dodać nowej oferty, ponieważ do tego zapytania jest otwarta oferta ze statusem => Toczy się. Zmień status oferty');
        }
        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::get()->map->only('id', 'nazwa_projektu'),
            'typs' => ['Klient oferuje', 'Klient na kontrakt'],
            'clients' => Client::get()->map->only('id', 'nazwa'),
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
            'statuses' => OfertaStatus::get()->map->only('id', 'name'),
            'krajs' => Kraj::get()->map->only('id', 'waluta'),
            'waluta' => Waluta::get()->map->only('id', 'name'),
            'zapytaniaById' => $zapytania->id,
            'clientById' => $client->id,
        ]);
    }
    public function store(OfertaStoreRequest $request)
    {
            $kurs = $this->changeRate($request->waluta_id, $request->kwota);
//            $checkStatusOpen = $this->checkStatusOpen($request->zapytania_id);

            $data = new Oferta();
            $data->zapytania_id = $request->zapytania_id;
            $data->typ = $request->typ;
            $data->client_id = $request->client_id;
            $data->data_wyslania = $request->data_wyslania;
            $data->kwota = $request->kwota;
            $data->waluta_id = $request->waluta_id;
            $data->kurs = $kurs[0];
            $data->kwotaPLN = $kurs[1];
            $data->data_kontakt = $request->data_kontakt;
            $data->oferta_status_id = $request->oferta_status_id;
            $data->opis = $request->opis;
            $data->user_id = $request->user_id;
            $data->save();

            $this->storeActivityLog('Nowa oferta', $data->id, $request->client_id, 'oferta', 'zmiany', Auth::id());

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
                'waluta_id' => $oferta->waluta_id,
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
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->withTrashed()->get(),
            'clientById' => Client::select('id', 'nazwa')->where('id', $oferta->client_id)->withTrashed()->firstOrFail(),
            'zapytaniaById' => Zapytania::select('id', 'nazwa_projektu')->where('id', $oferta->zapytania_id)->withTrashed()->firstOrFail(),
            'statuses' => OfertaStatus::get()->map->only('id', 'name'),
            'waluta' => Waluta::get()->map->only('id', 'name'),
        ]);
    }

    public function update(Oferta $oferta, OfertaStoreRequest $request)
    {
        $oferta->update($request->all());
        $this->saveRate($oferta->id, $request->kurs, $request->kwota);
        $this->storeActivityLog('Poprawiono ofertę', $oferta->id, $request->client_id, 'oferta', 'zmiany', Auth::id());

        return Redirect::route('oferta')->with('success', 'Oferta poprawiona.');
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
    public function exchangeRate($id)
    {
        $currency = Kursy::select('kurs')->where('waluta_id', $id)->latest()->first()->toArray();
        return (string) $currency['kurs'];
    }
    public function changeRate($waluta, $kwota)
    {
        $waluta = Waluta::where('id', $waluta)->pluck('id');
        $kurs = $this->exchangeRate($waluta[0]);
        $kwotaPLN = (float) $kwota * (float) $kurs;

        return [(float) $kurs, (float) $kwotaPLN];
    }
    public function saveRate($id, $kurs, $kwotaPLN)
    {
        $data = Oferta::find($id);
        $data->kurs = (float) $kurs;
        $data->kwotaPLN = $kwotaPLN;
        $data->save();
    }
    public function checkStatusOpen($id)
    {
        $oferta =  Oferta::with('status')
            ->where('ofertas.zapytania_id', $id)
            ->orWhereHas('status', function ($query) {
                $query->where('name', 'like', 'Przegrana');
                $query->orWhere('name', 'like', 'Wygrana');
            })->firstOrFail();

        if ($oferta->count() > 0) {
            return Redirect::route('oferta.edit', $oferta->id)->with('success', 'Zmień status oferty');
        }
    }
}
