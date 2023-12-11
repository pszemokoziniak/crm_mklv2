<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Zapytania;
use Carbon\Carbon;
use DateTimeImmutable;
//use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;


class CalendarController extends Controller
{
//    protected $numberDays;
//
//    protected \DateTimeImmutable $start;
//    protected \$end;

//    public function __construct(
//        public DateTimeImmutable $start,
//        public DateTimeImmutable $end,
//    ) {
//        $this->start = $start;
//        $this->end = $end;
//    }
    public function index()
    {
        $now = Carbon::now();
        $start = $now->format('Y-m-d');
        $end = $now->addDays(30)->format('Y-m-d');

        $start = array_values(Request::all('start'))[0]?array_values(Request::all('start'))[0]:$start;
        $end = array_values(Request::all('end'))[0]?array_values(Request::all('end'))[0]:$end;

//        $test = $_GET["search"]?$_GET["search"]:null;

        $daysN = $this->getNumberDays($start, $end);
//        $zapytanias = $this->getZapytania($start, $end);
//        dd($zapytanias);
        return Inertia::render('Calendar/Index', [
            'filters' => Request::all('search', 'start', 'end'),
            'daysN' => $daysN,
            'months' => $this->getMonths($daysN, $start),
            'days' => $this->getDays($daysN, $start),
            'zapytanias' => $this->getZapytania($start, $end),
            'start' => $start,
            'end' => $end,
        ]);
    }
    public function getNumberDays($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        return $numberDays = $start->diffInDays($end);
    }
    public function getMonths($daysN, $start)
    {
        $start = Carbon::parse($start)->addDays(-1);
        for ($i= 0; $i<= $daysN; $i++)
        {
            $months[] = $start->addDays(1)->format('m-Y');
        }

        return $months = array_count_values($months);
    }

    public function getDays($daysN, $start)
    {
        $start = Carbon::parse($start)->addDays(-1);
        for ($i= 0; $i<= $daysN; $i++)
        {
            $days[] = $start->addDays(1)->format('d');
        }
        return $days;
    }
    public function getDaysFull($daysN, $start)
    {
        $start = Carbon::parse($start);
        for ($i= 0; $i<= $daysN; $i++)
        {
            $daysFull[] = $start->addDays(1)->format('Y-m-d');
        }
        return $daysFull;
    }

    public function getZapytania($start, $end)
    {
        $search = array_values(Request::all('search'))[0];

        $zapytanias = Zapytania::with('oferta')
            ->with('client')
            ->where(function ($query) use ($search){
                $query->where('nazwa_projektu', 'like', '%'.$search.'%');
            })
            ->where(function ($query) use ($start, $end){
            $query->where('start', '>=', $start)
                  ->where('end', '<=', $end);
        })->orWhere(function ($query) use ($start) {
            $query->where('start', '<', $start)
                  ->where('end', '>', $start);
        })->orWhere(function ($query) use ($end) {
            $query->where('start', '<', $end)
                ->where('end', '>', $end);
        })->orderBy('start')
            ->filter(Request::only('search', 'start', 'end'))
            ->get();

        $data = array();
//dd($zapytanias);
        foreach ($zapytanias as $item) {

            $colSpan = $this->getColSpan($start, $end, $item->start, $item->end);
            array_push($data, ['id' => $item->id, 'id_zapyt' => $item->id_zapyt, 'nazwa_projektu' => $item->nazwa_projektu, 'oferta' => $item->oferta, 'client' => $item->client, 'start' => $item->start, 'end' => $item->end, 'colSpan' => $colSpan]);
        }

        return $data;
    }
    public function getColSpan($start, $end, $startZap, $endZap)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $startZap = Carbon::parse($startZap);
        $endZap = Carbon::parse($endZap);
//        $col =array();
//        $colCount =array();



        if ($endZap >= $end && $startZap >= $start)
        {
//            $colCount =array();
            $colStart = ($startZap->diffInDays($start));
            $colCount = $end->diffInDays($startZap)+1;
            $col = array(
                array($colStart, 0),
                array($colCount, 1),
            );
        }

        if ($endZap >= $end && $startZap <= $start )
        {
            $colCount = ($end->diffInDays($start)+1);
            $col = array(
                array($colCount, 1),
            );
        }

        if ($endZap <= $end && $startZap >= $start )
        {
            $colStart = ($startZap->diffInDays($start));
            $colCount = $endZap->diffInDays($startZap)+1;
            $col = array(
                array($colStart, 0),
                array($colCount, 1),
            );
        }

        if ($endZap < $end && $startZap < $start)
        {
            $colCount = ($endZap->diffInDays($start)+1);
            $col = array(
                array($colCount, 1),
            );
        }

        return $col;
    }
}
