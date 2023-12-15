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

        $this->clientZapytania();

        return Inertia::render('Stats/Index',[
            'start' => $start,
            'end' => $end,
            'filters' => Request::all('start', 'end'),
            'clientNumber' => $this->clientNumber(),
            'clientNumberByUser' => $this->clientNumberByUser($start, $end),
            'clientActive' => $this->clientActive(),
            'increaseClients' => $this->increaseClients($start, $end),
            'clientBranza' => $this->clientBranza($start, $end),
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
    public function clientZapytania()
    {
        $data = Oferta::select(DB::raw('clients.id, clients.nazwa, SUM(ofertas.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'ofertas.client_id')
            ->groupBy('clients.id', 'clients.nazwa')
            ->get();

        dd($data);
    }

}
