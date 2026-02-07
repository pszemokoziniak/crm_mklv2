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
        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->get(),
            'clients' => Client::select('id', 'nazwa')->get(),
            'users' => User::select('id', 'first_name', 'last_name')->get(),
            'statuses' => OfertaStatus::select('id', 'name')->get(),
            'waluta' => Waluta::select('id', 'name')->get(),
        ]);
    }

    public function createData(Zapytania $zapytania, Client $client)
    {
        $oferta = $this->checkStatusOpen($zapytania->id);

        if ($oferta !== null) {
            return Redirect::route('oferta.edit', $oferta->id)->with('error', 'Nie można dodać nowej oferty, ponieważ do tego zapytania jest otwarta oferta ze statusem => Toczy się.');
        }

        return Inertia::render('Oferta/Create', [
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->get(),
            'clients' => Client::select('id', 'nazwa')->get(),
            'statuses' => OfertaStatus::select('id', 'name')->get(),
            'waluta' => Waluta::select('id', 'name')->get(),
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
        return Inertia::render('Oferta/Edit', [
            'oferta' => $oferta,
            'clients' => Client::select('id', 'nazwa')->get(),
            'zapytanie' => Zapytania::select('id', 'nazwa_projektu')->withTrashed()->get(),
            'clientById' => Client::select('id', 'nazwa')->where('id', $oferta->client_id)->withTrashed()->first(),
            'zapytaniaById' => Zapytania::select('id', 'nazwa_projektu')->where('id', $oferta->zapytania_id)->withTrashed()->first(),
            'statuses' => OfertaStatus::select('id', 'name')->get(),
            'waluta' => Waluta::select('id', 'name')->get(),
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
