<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Oferta;
use App\Models\OfertaStatus;
use App\Models\Zapytania;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index()
    {
        $defaultStart = (new Carbon('first day of September 2018', 'Europe/Warsaw'))->format('Y-m-d');
        $defaultEnd = Carbon::now()->format('Y-m-d');

        $start = Request::input('start') ?: $defaultStart;
        // $end powiekszamy do konca dnia, zeby przedzial byl inkluzywny dla rekordow z koncowej daty
        $endRaw = Request::input('end') ?: $defaultEnd;
        $end = Carbon::parse($endRaw)->endOfDay()->format('Y-m-d H:i:s');

        Log::info('Stats: filter', ['start' => $start, 'end' => $end, 'raw_query' => Request::query()]);

        return Inertia::render('Stats/Index', [
            'start' => $start,
            'end' => $endRaw,
            'filters' => Request::all('start', 'end'),
            'clientNumber' => $this->clientNumber($start, $end),
            'clientNumberByUser' => $this->clientNumberByUser($start, $end),
            'clientActive' => $this->clientActive($start, $end),
            'increaseClients' => $this->increaseClients($start, $end),
            'clientBranza' => $this->clientBranza($start, $end),
            'clientZapytaniaSumAmount' => $this->clientZapytaniaSumAmount($start, $end),
            'clientOfertaSumAmount' => $this->clientOfertaSumAmount($start, $end),
            'quantityZapytania' => $this->quantityZapytania($start, $end),
            'zapytaniaOfertySumAmount' => $this->zapytaniaOfertySumAmount($start, $end),
            'zapytaniaBranze' => $this->zapytaniaBranze($start, $end),
            'zapytaniaZakres' => $this->zapytaniaZakres($start, $end),
            'zapytaniaUsers' => $this->zapytaniaUsers($start, $end),
            'quantityOferta' => $this->quantityOferta($start, $end),
            'ofertaStatus' => $this->ofertaStatus($start, $end),
            'ofertaStatusWin' => $this->ofertaStatusWin($start, $end),
        ]);
    }

    public function clientNumber($start, $end)
    {
        return Client::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->count();
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

    public function clientActive($start, $end)
    {
        $activeClient = (int) Zapytania::select('client_id')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->groupBy('client_id')
            ->get()->count();
        $nonActiveClient = (int) $this->clientNumber($start, $end) - $activeClient;
        if ($nonActiveClient < 0) {
            $nonActiveClient = 0;
        }
        return [$activeClient, $nonActiveClient];
    }

    public function increaseClients($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $start->setDay(1);

        $months = [];
        $countMonth = [];

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $months[] = $month->format('m-Y');
        }

        foreach ($months as $month) {
            $startMonth = Carbon::createFromFormat('m-Y', $month)->firstOfMonth();
            $endMonth = Carbon::createFromFormat('m-Y', $month)->endOfMonth();
            $count = Client::where('created_at', '>=', $startMonth)
                ->where('created_at', '<=', $endMonth)
                ->count();
            $countMonth[] = $count;
        }

        return [$months, $countMonth];
    }

    public function clientBranza($start, $end)
    {
        $data = Client::select(DB::raw('branzas.id, branzas.name, COUNT(*) AS count'))
            ->join('branzas', 'branzas.id', '=', 'clients.branza_id')
            ->where('clients.created_at', '>=', $start)
            ->where('clients.created_at', '<=', $end)
            ->groupBy('branzas.id', 'branzas.name')
            ->orderBy('count', 'DESC')
            ->get();

        $name = [];
        $count = [];
        foreach ($data as $item) {
            $name[] = $item->name;
            $count[] = $item->count;
        }
        return [$name, $count];
    }

    public function clientZapytaniaSumAmount($start, $end)
    {
        $data = Zapytania::select(DB::raw('clients.id, clients.nazwa, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'zapytanias.client_id')
            ->where('zapytanias.created_at', '>=', $start)
            ->where('zapytanias.created_at', '<=', $end)
            ->groupBy('clients.nazwa', 'clients.id')
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->nazwa;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }

    public function clientOfertaSumAmount($start, $end)
    {
        $data = Oferta::select(DB::raw('clients.id, clients.nazwa, SUM(ofertas.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'ofertas.client_id')
            ->where('ofertas.created_at', '>=', $start)
            ->where('ofertas.created_at', '<=', $end)
            ->groupBy('clients.nazwa', 'clients.id')
            ->orderBy('count', 'DESC')
            ->limit(15)
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->nazwa;
            $amounts[] = round((float) $item->count, 0);
        }
        return [$labels, $amounts];
    }

    public function quantityZapytania($start, $end)
    {
        $base = Zapytania::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end);

        $count = (clone $base)->count();
        $sum = (float) (clone $base)->sum('kwotaPLN');

        $withOferta = (clone $base)->whereHas('oferty')->count();
        $withOfertaSum = (float) (clone $base)->whereHas('oferty')->sum('kwotaPLN');

        $withoutOferta = $count - $withOferta;
        $withoutOfertaSum = $sum - $withOfertaSum;

        return [
            'count' => $count,
            'sum' => $sum,
            'breakdown' => [
                ['name' => 'Z ofertą', 'qty' => $withOferta, 'total' => round($withOfertaSum, 2)],
                ['name' => 'Bez oferty', 'qty' => $withoutOferta, 'total' => round($withoutOfertaSum, 2)],
            ],
        ];
    }

    public function zapytaniaOfertySumAmount($start, $end)
    {
        $start = Carbon::parse($start)->startOfMonth();
        $end = Carbon::parse($end);

        $months = [];
        $zapytaniaMonthSum = [];
        $ofertyMonthSum = [];
        $ofertyWygraneMonthSum = [];

        // Id statusu 'wygrana' (case-insensitive) - jezeli go nie ma, wygrane sumy beda zero
        $wygranaStatusId = OfertaStatus::whereRaw('LOWER(TRIM(name)) = ?', ['wygrana'])->value('id');

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $zapytaniaSum = (float) Zapytania::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('kwotaPLN');

            $ofertySum = (float) Oferta::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('kwotaPLN');

            $ofertyWygraneSum = 0;
            if ($wygranaStatusId) {
                $ofertyWygraneSum = (float) Oferta::where('oferta_status_id', $wygranaStatusId)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('kwotaPLN');
            }

            $months[] = $month->format('m-Y');
            $zapytaniaMonthSum[] = $zapytaniaSum;
            $ofertyMonthSum[] = $ofertySum;
            $ofertyWygraneMonthSum[] = $ofertyWygraneSum;
        }

        return [$months, $zapytaniaMonthSum, $ofertyMonthSum, $ofertyWygraneMonthSum];
    }

    public function zapytaniaBranze($start, $end)
    {
        $data = Zapytania::select(DB::raw('branzas.name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('clients', 'clients.id', '=', 'zapytanias.client_id')
            ->join('branzas', 'branzas.id', '=', 'clients.branza_id')
            ->where('zapytanias.created_at', '>=', $start)
            ->where('zapytanias.created_at', '<=', $end)
            ->groupBy('branzas.name')
            ->orderBy('count', 'DESC')
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }

    public function zapytaniaZakres($start, $end)
    {
        $data = Zapytania::select(DB::raw('zakres.id, zakres.name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('zakres', 'zakres.id', '=', 'zapytanias.zakres_id')
            ->where('zapytanias.created_at', '>=', $start)
            ->where('zapytanias.created_at', '<=', $end)
            ->groupBy('zakres.name', 'zakres.id')
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }

    public function zapytaniaUsers($start, $end)
    {
        $data = Zapytania::select(DB::raw('users.id, users.last_name, users.first_name, SUM(zapytanias.kwotaPLN) AS count'))
            ->join('users', 'users.id', '=', 'zapytanias.user_id')
            ->where('zapytanias.created_at', '>=', $start)
            ->where('zapytanias.created_at', '<=', $end)
            ->groupBy('users.last_name', 'users.first_name', 'users.id')
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->last_name . ' ' . $item->first_name;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }

    public function quantityOferta($start, $end)
    {
        $count = Oferta::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->count();
        $sum = (float) Oferta::where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->sum('kwotaPLN');

        $byStatus = Oferta::select(DB::raw('oferta_statuses.name, COUNT(*) as qty, SUM(ofertas.kwotaPLN) as total'))
            ->join('oferta_statuses', 'oferta_statuses.id', '=', 'ofertas.oferta_status_id')
            ->where('ofertas.created_at', '>=', $start)
            ->where('ofertas.created_at', '<=', $end)
            ->groupBy('oferta_statuses.name')
            ->orderBy('total', 'DESC')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'qty' => (int) $item->qty,
                'total' => round((float) $item->total, 2),
            ]);

        return [
            'count' => $count,
            'sum' => $sum,
            'byStatus' => $byStatus,
        ];
    }

    public function ofertaStatus($start, $end)
    {
        $data = Oferta::select(DB::raw('oferta_statuses.id, oferta_statuses.name, SUM(ofertas.kwotaPLN) AS count'))
            ->join('oferta_statuses', 'oferta_statuses.id', '=', 'ofertas.oferta_status_id')
            ->where('ofertas.created_at', '>=', $start)
            ->where('ofertas.created_at', '<=', $end)
            ->where(function ($query) {
                $query->where('oferta_statuses.name', 'Wygrana')
                      ->orWhere('oferta_statuses.name', 'Przegrana');
            })
            ->groupBy('oferta_statuses.name', 'oferta_statuses.id')
            ->get();

        if ($data->isEmpty()) {
            return [null, null];
        }

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }

    public function ofertaStatusWin($start, $end)
    {
        $data = Oferta::select(DB::raw('oferta_statuses.id, oferta_statuses.name, SUM(ofertas.kwotaPLN) AS count'))
            ->join('oferta_statuses', 'oferta_statuses.id', '=', 'ofertas.oferta_status_id')
            ->where('ofertas.created_at', '>=', $start)
            ->where('ofertas.created_at', '<=', $end)
            ->groupBy('oferta_statuses.name', 'oferta_statuses.id')
            ->get();

        $labels = [];
        $amounts = [];
        foreach ($data as $item) {
            $labels[] = $item->name;
            $amounts[] = $item->count;
        }
        return [$labels, $amounts];
    }
}
