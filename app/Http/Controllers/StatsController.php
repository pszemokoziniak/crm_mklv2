<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        $start = new Carbon($start);

        $this->increaseClients($start);

        return Inertia::render('Stats/Index',[
            'start' => $start,
            'end' => $end,
            'filters' => Request::all('start', 'end'),
            'clientNumber' => $this->clientNumber(),
            'clientNumberByNumber' => $this->clientNumberByName($start, $end),
            'clientActive' => $this->clientActive(),
            'increaseClients' => $this->increaseClients($start),
        ]);

    }
    public function clientNumber()
    {
        return Client::count();
    }
    public function clientNumberByName($start, $end)
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
    public function increaseClients(Carbon $start)
    {
        $start->setDay(1);
        foreach (CarbonPeriod::create($start, '1 month', Carbon::today()) as $month) {
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

}
