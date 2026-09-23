<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Zapytania;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        // Domyslny zakres: dzis -> 6 miesiecy do przodu.
        $defaultStart = $now->copy()->format('Y-m-d');
        $defaultEnd = $now->copy()->addMonths(6)->format('Y-m-d');

        $start = array_values(Request::all('start'))[0] ?: $defaultStart;
        $end = array_values(Request::all('end'))[0] ?: $defaultEnd;

        // Os czasu zawsze w tygodniach (kolumna = 1 tydzien, poniedzialek -> niedziela).
        $weeks = $this->getWeeks($start, $end);

        return Inertia::render('Calendar/Index', [
            'filters' => Request::all('search', 'start', 'end'),
            'weeks' => $weeks,
            'months' => $this->getMonths($weeks),
            'zapytanias' => $this->getZapytania($start, $end, $weeks),
            'start' => $start,
            'end' => $end,
        ]);
    }

    /**
     * Kolumny tygodniowe pokrywajace [start, end]. Siatka wyrownana do
     * poniedzialkow, zeby kolumny odpowiadaly kalendarzowym tygodniom.
     *
     * @return array<int, array{start: string, label: string, week: int, month: string}>
     */
    public function getWeeks($start, $end): array
    {
        $gridStart = Carbon::parse($start)->startOfWeek(Carbon::MONDAY);
        $lastWeek = Carbon::parse($end)->startOfWeek(Carbon::MONDAY);

        $weeks = [];
        for ($day = $gridStart->copy(); $day <= $lastWeek; $day->addWeek()) {
            // Tydzien przypisujemy do miesiaca swojego czwartku (standard ISO).
            $thursday = $day->copy()->addDays(3);
            $weeks[] = [
                'start' => $day->format('Y-m-d'),
                'label' => $day->format('d.m'),
                'week' => (int) $day->isoWeek(),
                'month' => $thursday->format('m-Y'),
            ];
        }

        return $weeks;
    }

    /**
     * Ilosc kolumn tygodniowych w kazdym miesiacu (kolejnosc chronologiczna) —
     * uzywane do colspan w wierszu miesiecy.
     *
     * @param  array<int, array{month: string}>  $weeks
     * @return array<string, int>
     */
    public function getMonths(array $weeks): array
    {
        $months = [];
        foreach ($weeks as $week) {
            $months[$week['month']] = ($months[$week['month']] ?? 0) + 1;
        }

        return $months;
    }

    public function getZapytania($start, $end, array $weeks)
    {
        $search = array_values(Request::all('search'))[0];

        $zapytanias = Zapytania::with('oferty', 'client')
            ->when($search, function ($query, $search) {
                $query->where('nazwa_projektu', 'like', '%'.$search.'%');
            })
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('start', '>=', $start)
                       ->where('end', '<=', $end);
                })->orWhere(function ($q) use ($start) {
                    $q->where('start', '<', $start)
                       ->where('end', '>', $start);
                })->orWhere(function ($q) use ($end) {
                    $q->where('start', '<', $end)
                       ->where('end', '>', $end);
                });
            })
            ->whereNotNull('start')
            ->whereNotNull('end')
            ->orderBy('start')
            ->get();

        $gridStart = $weeks[0]['start'] ?? $start;
        $totalCols = count($weeks);

        $data = [];

        foreach ($zapytanias as $item) {
            $colSpan = $this->getColSpan($gridStart, $totalCols, $item->start, $item->end);
            $data[] = [
                'id' => $item->id,
                'id_zapyt' => $item->id_zapyt,
                'nazwa_projektu' => $item->nazwa_projektu,
                'oferta' => $item->oferty,
                'client' => $item->client,
                'start' => $item->start,
                'end' => $item->end,
                'colSpan' => $colSpan,
            ];
        }

        return $data;
    }

    /**
     * Pozycja paska w kolumnach TYGODNIOWYCH: pusta rozbiegowka + wlasciwy pasek.
     * Zwraca pary [liczba_kolumn, flaga] — flaga 0 = puste, 1 = pasek.
     *
     * @return array<int, array{0: int, 1: int}>
     */
    public function getColSpan($gridStartYmd, int $totalCols, $startZap, $endZap): array
    {
        if ($totalCols <= 0) {
            return [];
        }

        $gridStart = Carbon::parse($gridStartYmd); // poniedzialek pierwszej kolumny
        $s = Carbon::parse($startZap);
        $e = Carbon::parse($endZap);

        // Kolumna = ktory tydzien od poczatku siatki (0-indeks).
        $startCol = intdiv(max(0, $gridStart->diffInDays($s, false)), 7);
        $endCol = intdiv($gridStart->diffInDays($e, false), 7);

        // Docinamy do widocznej siatki.
        $startCol = max(0, min($startCol, $totalCols - 1));
        $endCol = max(0, min($endCol, $totalCols - 1));

        if ($endCol < $startCol) {
            $endCol = $startCol;
        }

        $leading = $startCol;
        $span = $endCol - $startCol + 1;

        if ($leading > 0) {
            return [[$leading, 0], [$span, 1]];
        }

        return [[$span, 1]];
    }
}
