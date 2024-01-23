<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiwumStoreRequest;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\ContactStoreRequest;
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Mail\ZapytaniaMail;
use App\Models\ArchiwumZapytania;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\Kursy;
use App\Models\Oferta;
use App\Models\User;
use App\Models\Zakres;
use App\Models\Zapytania;
use Carbon\Carbon;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;

class ZapytaniaController extends Controller
{
    use StoreActivityLog;
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

        return Inertia::render('Zapytania/Create', [
            'zakres' => Zakres::get()->map->only('id', 'name'),
            'kraj' => Kraj::get()->map->only('id', 'name', 'waluta'),
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::get()->map->only('id', 'nazwa'),
            'id_zapyt' => (Zapytania::withTrashed()->count()+1).'/'.Carbon::now()->format('Y'),
        ]);
    }
    public function store(ZapytaniaStoreRequest $request)
    {
//        Zapytania::create($request->all());
        $walutaName = Kraj::where('id', $request->waluta)->pluck('waluta');
        (int) $kwotaPLN = (int) $request->kwota * (float) $this->exchangeRate($walutaName[0]);
        (float) $kurs = $this->exchangeRate($walutaName[0]);
        $data = new Zapytania();
        $data->id_zapyt = $request->id_zapyt;
        $data->user_otrzymal_id = $request->user_otrzymal_id;
        $data->data_otrzymania = $request->data_otrzymania;
        $data->data_zlozenia = $request->data_zlozenia;
        $data->client_id = $request->client_id;
        $data->nazwa_projektu = $request->nazwa_projektu;
        $data->miejscowosc = $request->miejscowosc;
        $data->kwotaPLN = $kwotaPLN;
        $data->kurs = $kurs;
        $data->kraj_id = $request->kraj_id;
        $data->zakres_id = $request->zakres_id;
        $data->user_opracowuje_id = $request->user_opracowuje_id;
        $data->start = $request->start;
        $data->end = $request->end;
        $data->kwota = $request->kwota;
        $data->waluta = $request->waluta;
        $data->opis = $request->opis;
        $data->user_id = $request->user_id;
        $data->save();

        ($data)??$this->storeActivityLog('Nowe zapytanie', $data->id, $request->client_id, 'zapytania', 'zmiany', Auth::id());

        Mail::send(new ZapytaniaMail($this->zapytaniaById($data->id)));

        return Redirect::route('zapytania')->with('success', 'Zapisano. Mail wysłany');
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
            'clientById' => Client::where('id', $zapytania->client_id)->withTrashed()->firstOrFail(),
            'archiwumOpis' => ArchiwumZapytania::with('user')->where('zapytania_id', $zapytania->id)->get(),
        ]);
    }

    public function update(Zapytania $zapytania, ZapytaniaStoreRequest $request)
    {
        $zapytania->update($request->all());

        ($zapytania)??$this->storeActivityLog('Poprawiono zapytanie', $zapytania->id, $request->client_id, 'zapytania', 'zmiany', Auth::id());


        return Redirect::route('zapytania')->with('success', 'Zapytanie poprawione.');
    }

    public function destroy(Zapytania $zapytania, ArchiwumStoreRequest $request)
    {
        ArchiwumZapytania::create($request->all());
        Oferta::where('zapytania_id', $zapytania->id)->delete();

        $zapytania->delete();

        return Redirect::route('zapytania.edit', $zapytania->id)->with('success', 'Zapytanie zarchiwizowane.');
    }
    public function archiwum($id)
    {
        $zapytania = Zapytania::where('id', $id)->withTrashed()->get()->map->only('id', 'id_zapyt', 'nazwa_projektu');

        return Inertia::render('Zapytania/Archiwum', [
            'zapytania' => $zapytania,
        ]);
    }
    public function restore(Zapytania $zapytania)
    {
        $zapytania->restore();

        return Redirect::back()->with('success', 'Zapytanie przywrócone');
    }
    public function exchangeRate($currency)
    {
        $currency = Kursy::select('kurs')->where('name', $currency)->latest()->first()->toArray();
        $currency = $currency['kurs'];

        return (string) $currency;
    }
    public function kwotaPLN($amount, $currency)
    {
        $currency = Kursy::select('kurs')->where('id', $currency)->latest()->first()->toArray();
        $kwotaPLN = ($amount * $currency['kurs']);

        return (float) $kwotaPLN;
    }
    public function pdf(Zapytania $zapytania)
    {
        $data = Zapytania::with('client')
            ->with('user')
            ->with('kraj')
            ->with('zakres')
            ->get();

        $data = $this->zapytaniaById($zapytania->id);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('zapytaniaPdf', compact('data'));
        return $pdf->stream('zapytanie'.$zapytania->id_zapyt.'.pdf');
    }
    public function zapytaniaById($id)
    {
        $data = Zapytania::with('client')
            ->with('user')
            ->with('kraj')
            ->with('zakres')
            ->where('zapytanias.id', $id)
            ->firstOrFail();

        return $data;
    }
    public function mail(Zapytania $zapytania)
    {
        Mail::send(new ZapytaniaMail($this->zapytaniaById($zapytania->id)));
    }

}
