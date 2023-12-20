<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Oferta;
use App\Models\Zapytania;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index()
    {
        $start = new Carbon('first day of September 2018', 'Europe/Warsaw');
        $start = $start->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');

        $start = array_values(Request::all('start'))[0]?array_values(Request::all('start'))[0]:$start;
        $end = array_values(Request::all('end'))[0]?array_values(Request::all('end'))[0]:$end;

//        dd($this->zapytaniaBranze());

        return Inertia::render('Stats/Index',[
            'start' => $start,
            'end' => $end,
            'filters' => Request::all('start', 'end'),
            'clientNumber' => $this->clientNumber(),
            'clientNumberByUser' => $this->clientNumberByUser($start, $end),
            'clientActive' => $this->clientActive(),
            'increaseClients' => $this->increaseClients($start, $end),
            'clientBranza' => $this->clientBranza($start, $end),
            'clientZapytaniaSumAmount' => $this->clientZapytaniaSumAmount(),
            'clientOfertaSumAmount' => $this->clientOfertaSumAmount(),
            'quantityZapytania' => $this->quantityZapytania(),
            'zapytaniaOfertySumAmount' => $this->zapytaniaOfertySumAmount($start, $end),
            'zapytaniaBranze' => $this->zapytaniaBranze(),
            'zapytaniaZakres' => $this->zapytaniaZakres(),
            'zapytaniaUsers' => $this->zapytaniaUsers(),
            'quantityOferta' => $this->quantityOferta(),
            'ofertaStatus' => $this->ofertaStatus(),
            'ofertaStatusWin' => $this->ofertaStatusWin(),
        ]);

    }
    public function clientNumber()
    {
        return Client::count();
    }
    public function clientNumberByUser($start, $end)
    {
        return Client::select(DB::raw('users.id, users.last_name, users.first_name, COUNT(*) AS count'))
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->where('clients.created_at', '>=', $start)
            ->where('clients.created_at', '<=', $end)
            ->groupBy('users.id', 'users.last_name', 'users.first_name', 'users.created_at')
            ->orderBy('count', 'DESC')
            ->get();
    }
    public function clientActive()
    {
        $activeClient = (int) Zapytania::select('client_id')->groupBy('client_id')->get()->count();
        $nonActiveClient = (int) $this->clientNumber() - $activeClient;
        return [$activeClient, $nonActiveClient];
    }
    public function increaseClients($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $start = array_values(Request::all('start'))[0]?Carbon::parse(array_values(Request::all('start'))[0]):Carbon::parse($start);
        $end = array_values(Request::all('end'))[0]?Carbon::parse(array_values(Request::all('end'))[0]):Carbon::parse($end);

        $start->setDay(1);
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $months[] = $month->format('m-Y');
        }

        foreach ($months as $month) {
            $startMonth = Carbon::createFromFormat('m-Y', $month)->firstOfMonth();
            $endMonth = Carbon::createFromFormat('m-Y', $month)->endOfMonth();
            $count = Client::where('created_at', '>=', $startMonth)
                ->where('created_at', '<=', $endMonth)
                ->get()
                ->count();
            $countMonth[] = $count;
        }
        return [$months, $countMonth];
    }
    public function clientBranza($start, $end)
    {
        $clientBranza = Client::select(DB::raw('branzas.id, branzas.name, COUNT(*) AS count'))
            ->join('branzas', 'branzas.id', '=', 'clients.branza_id')
            ->where('clients.created_at', '>=', $start)
            ->where('clients.created_at', '<=', $end)
            ->groupBy('branzas.id', 'branzas.name')
            ->orderBy('count', 'DESC')
            ->get();

        foreach($clientBranza as $item) {
            $name[] = $item->name;
            $count[] = $item->count;
        }
        return [$name, $count];
    }
    public function clientZapytaniaSumAmount()
    {
        $data = Zapytania::select(DB::raw('clients.id, clients.nazwa, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'zapytanias.client_id')
            ->groupBy('clients.nazwa', 'clients.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->nazwa;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function clientOfertaSumAmount()
    {
        $data = Oferta::select(DB::raw('clients.id, clients.nazwa, SUM(ofertas.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'ofertas.client_id')
            ->groupBy('clients.nazwa', 'clients.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->nazwa;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function quantityZapytania()
    {
        (int) $count = Zapytania::get()->count();
        (float) $sum = Zapytania::sum('kwotaPLN');

        return [$count, $sum];
    }
    public function zapytaniaOfertySumAmount($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $start = array_values(Request::all('start'))[0]?Carbon::parse(array_values(Request::all('start'))[0]):Carbon::parse($start);
        $end = array_values(Request::all('end'))[0]?Carbon::parse(array_values(Request::all('end'))[0]):Carbon::parse($end);
        $start->setDay(1);
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $zapytania = Zapytania::select('start', 'kwotaPLN')
                ->whereMonth('start', $month->format('m'))
                ->whereYear('start', $month->format('Y'))
                ->get()->sum('kwotaPLN');

            $oferty = Zapytania::with('oferta')
//                ->select('zapytanias.start', 'ofertas.kwotaPLN')
                ->whereMonth('start', $month->format('m'))
                ->whereYear('start', $month->format('Y'))
                ->get()->sum('oferta.kwotaPLN');

            $ofertyWygrane = Zapytania::with('oferta')
                ->whereMonth('start', $month->format('m'))
                ->whereYear('start', $month->format('Y'))
//                ->where('oferta.oferta_status_id', '603f809e-aa41-49be-b25f-2166dd93bd5e')
                ->whereHas('oferta', function ($query) {
                    $query->where('oferta_status_id', '603f809e-aa41-49be-b25f-2166dd93bd5e');
                })->get()->sum('oferta.kwotaPLN');

            $zapytaniaMonthSum[] = $zapytania;
            $ofertyMonthSum[] = $oferty;
            $ofertyWygraneMonthSum[] = $ofertyWygrane;
            $months[] = $month->format('m-Y');
        }

        return [$months, $zapytaniaMonthSum, $ofertyMonthSum, $ofertyWygraneMonthSum];
    }

    public function zapytaniaBranze()
    {
        $data = Zapytania::select(DB::raw('clients.id, branzas.name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'zapytanias.client_id')
            ->join('branzas', 'branzas.id', '=', 'clients.branza_id')
            ->groupBy('branzas.name', 'clients.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function zapytaniaZakres()
    {
        $data = Zapytania::select(DB::raw('zakres.id, zakres.name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('zakres', 'zakres.id', '=', 'zapytanias.zakres_id')
            ->groupBy('zakres.name', 'zakres.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function zapytaniaUsers()
    {

        $data = Zapytania::select(DB::raw('users.id, users.last_name, users.first_name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('users', 'users.id', '=', 'zapytanias.user_id')
            ->groupBy('users.last_name', 'users.first_name', 'users.id')
            ->get();

       foreach ($data as $item) {
            $labels[] = $item->last_name.' '.$item->first_name;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function quantityOferta()
    {
        (int) $count = Oferta::get()->count();
        (float) $sum = Oferta::sum('kwotaPLN');

        return [$count, number_format($sum, 2)];
    }
    public function ofertaStatus()
    {

        $data = Oferta::select(DB::raw('oferta_statuses.id, oferta_statuses.name, SUM(ofertas.kwotaPLN) AS count'))
            ->join('oferta_statuses', 'oferta_statuses.id', '=', 'ofertas.oferta_status_id')
            ->where('oferta_statuses.name', 'Wygrana')
            ->orWhere('oferta_statuses.name', 'Przegrana')
            ->groupBy('oferta_statuses.name', 'oferta_statuses.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
    public function ofertaStatusWin()
    {

        $data = Oferta::select(DB::raw('oferta_statuses.id, oferta_statuses.name, SUM(ofertas.kwotaPLN) AS count'))
            ->join('oferta_statuses', 'oferta_statuses.id', '=', 'ofertas.oferta_status_id')
            ->groupBy('oferta_statuses.name', 'oferta_statuses.id')
            ->get();

        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels,$amounts];
    }
}
