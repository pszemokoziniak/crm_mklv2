<?php

namespace App\Http\Controllers;

use App\Models\Kontakt;
use App\Models\Oferta;
use App\Models\Zadania;
use App\Models\Zapytania;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DeadlinesController extends Controller
{
    /**
     * Widok kalendarza terminow. Miesiac po miesiacu.
     * Query params: ?month=YYYY-MM (default: biezacy)
     */
    public function index()
    {
        // Miesiac
        $monthParam = Request::input('month');
        try {
            $viewMonth = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : Carbon::now()->startOfMonth();
        } catch (\Exception $e) {
            $viewMonth = Carbon::now()->startOfMonth();
        }

        // Range - pierwszy dzien tygodnia zawierajacego 1. dnia miesiaca do ostatniego dnia tygodnia z ostatnim
        $rangeStart = $viewMonth->copy()->startOfWeek(Carbon::MONDAY);
        $rangeEnd = $viewMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Uprawnienia: tylko super-admin i Administrator widza wszystkich i moga filtrowac per user.
        // Pozostali (Kierownictwo, Eksport, Techniczny, Praktyki) - tylko wlasne rekordy.
        $user = Auth::user();
        $hasFullAccess = $user->hasAnyRole(['super-admin', 'Administrator']);
        $selectedUserId = Request::get('user_id');
        $filterUserId = $hasFullAccess ? ($selectedUserId ? (int) $selectedUserId : null) : $user->id;

        $events = [];

        // 1. Zapytania z data_zlozenia w zakresie (bez ofert = jeszcze do zlozenia)
        Zapytania::with(['client:id,nazwa', 'opracowuje:id,first_name,last_name'])
            ->whereBetween('data_zlozenia', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->whereDoesntHave('oferty', fn ($q) => $q->whereNull('deleted_at'))
            ->when($filterUserId, fn ($q) => $q->where(fn ($sq) => $sq->where('user_opracowuje_id', $filterUserId)->orWhere('user_id', $filterUserId)))
            ->get()
            ->each(function ($z) use (&$events) {
                $events[] = [
                    'type' => 'zapytanie',
                    'label' => 'Zapytanie',
                    'color' => 'indigo',
                    'date' => $z->data_zlozenia->format('Y-m-d'),
                    'title' => $z->nazwa_projektu ?: '(bez nazwy)',
                    'subtitle' => ($z->id_zapyt ? $z->id_zapyt . ' · ' : '') . ($z->client?->nazwa ?: 'brak klienta'),
                    'assignee' => $z->opracowuje ? trim($z->opracowuje->first_name . ' ' . $z->opracowuje->last_name) : null,
                    'link' => "/zapytania/{$z->id}/edit",
                ];
            });

        // 2. Oferty w statusie TOCZY z data_kontakt w zakresie
        Oferta::with(['zapytania:id,nazwa_projektu', 'client:id,nazwa', 'user:id,first_name,last_name'])
            ->whereHas('ofertastatus', fn ($q) => $q->where('name', 'TOCZY'))
            ->whereBetween('data_kontakt', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->when($filterUserId, fn ($q) => $q->where('user_id', $filterUserId))
            ->get()
            ->each(function ($o) use (&$events) {
                $events[] = [
                    'type' => 'oferta',
                    'label' => 'Oferta',
                    'color' => 'green',
                    'date' => $o->data_kontakt->format('Y-m-d'),
                    'title' => $o->zapytania?->nazwa_projektu ?: 'Brak projektu',
                    'subtitle' => ($o->client?->nazwa ?: 'brak klienta') . ' · #' . $o->id,
                    'assignee' => $o->user ? trim($o->user->first_name . ' ' . $o->user->last_name) : null,
                    'link' => "/oferta/{$o->id}/edit",
                ];
            });

        // 3. Kontakty (najnowsze per watek) z next_call_date w zakresie
        Kontakt::with(['client:id,nazwa', 'opiekun:id,first_name,last_name'])
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')->from('kontakts')->groupByRaw('COALESCE(parent_id, id)');
            })
            ->whereBetween('next_call_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->when($filterUserId, fn ($q) => $q->where('opiekun_id', $filterUserId))
            ->get()
            ->each(function ($k) use (&$events) {
                $events[] = [
                    'type' => 'kontakt',
                    'label' => 'Kontakt',
                    'color' => 'blue',
                    'date' => $k->next_call_date->format('Y-m-d'),
                    'time' => $k->next_call_time,
                    'title' => $k->client?->nazwa ?: 'Brak klienta',
                    'subtitle' => $k->subject ?: '(bez tematu)',
                    'assignee' => $k->opiekun ? trim($k->opiekun->first_name . ' ' . $k->opiekun->last_name) : null,
                    'link' => '/kontakt/' . ($k->parent_id ?? $k->id) . '/edit',
                ];
            });

        // 4. Zadania aktywne z deadline w zakresie
        Zadania::with(['client:id,nazwa', 'responsiblePerson:id,first_name,last_name'])
            ->where('status', '!=', 'zamkniete')
            ->whereBetween('deadline', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->when($filterUserId, fn ($q) => $q->where('responsible_person_id', $filterUserId))
            ->get()
            ->each(function ($z) use (&$events) {
                $events[] = [
                    'type' => 'zadanie',
                    'label' => 'Zadanie',
                    'color' => 'orange',
                    'date' => $z->deadline->format('Y-m-d'),
                    'title' => $z->subject ?: '(bez tematu)',
                    'subtitle' => $z->client?->nazwa ?: null,
                    'assignee' => $z->responsiblePerson ? trim($z->responsiblePerson->first_name . ' ' . $z->responsiblePerson->last_name) : null,
                    'link' => "/zadania/{$z->id}/edit",
                ];
            });

        // Grupujemy per data (YYYY-MM-DD => tablica zdarzen)
        $eventsByDate = collect($events)->groupBy('date')->map(fn ($items) => $items->values())->toArray();

        // Siatka - lista dni w widoku (od rangeStart do rangeEnd)
        $days = [];
        $cursor = $rangeStart->copy();
        while ($cursor <= $rangeEnd) {
            $iso = $cursor->format('Y-m-d');
            $days[] = [
                'date' => $iso,
                'day' => (int) $cursor->format('d'),
                'inCurrentMonth' => $cursor->month === $viewMonth->month,
                'isToday' => $cursor->isToday(),
                'isWeekend' => $cursor->isWeekend(),
                'events' => $eventsByDate[$iso] ?? [],
            ];
            $cursor->addDay();
        }

        $plMonths = [1 => 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień'];

        return Inertia::render('Deadlines/Index', [
            'viewMonth' => $viewMonth->format('Y-m'),
            'viewMonthLabel' => $plMonths[(int) $viewMonth->format('n')] . ' ' . $viewMonth->format('Y'),
            'prevMonth' => $viewMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $viewMonth->copy()->addMonth()->format('Y-m'),
            'today' => Carbon::now()->format('Y-m'),
            'days' => $days,
            'stats' => [
                'zapytania' => collect($events)->where('type', 'zapytanie')->count(),
                'oferty' => collect($events)->where('type', 'oferta')->count(),
                'kontakty' => collect($events)->where('type', 'kontakt')->count(),
                'zadania' => collect($events)->where('type', 'zadanie')->count(),
            ],
            'users' => $hasFullAccess ? \App\Models\User::select('id', 'first_name', 'last_name')->orderBy(\Illuminate\Support\Facades\DB::raw('TRIM(last_name)'))->get() : [],
            'selectedUserId' => $selectedUserId ? (int) $selectedUserId : null,
        ]);
    }
}
