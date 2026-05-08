<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use App\Models\Client;
use App\Models\FutureProject;
use App\Models\Kontakt;
use App\Models\Oferta;
use App\Models\Zadania;
use App\Models\Zapytania;
use App\Models\ZapytaniaWznowienie; // Added ZapytaniaWznowienie model
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Kierownictwo: super-admin i administrator
        $isKierownictwo = $user->hasAnyRole(['super-admin', 'administrator']);
        $selectedUserId = Request::get('user_id');

        // Jeśli Kierownictwo nie wybrało nikogo, $filterUserId jest null (widzą wszystko).
        // Jeśli to nie Kierownictwo (np. Eksport Techniczny), zawsze filtrujemy po ich ID.
        $filterUserId = $isKierownictwo ? $selectedUserId : $user->id;

        // === Statystyki miesiąc do miesiąca ===
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $statsQuery = function ($model, $dateColumn, $aggregate, $aggregateColumn = null) use ($currentMonthStart, $currentMonthEnd, $previousMonthStart, $previousMonthEnd, $filterUserId) {
            $baseQuery = $model::query();

            if ($filterUserId) {
                $userColumn = 'user_id';
                if ($model === Zapytania::class) {
                    $userColumn = 'user_opracowuje_id';
                }
                $baseQuery->where($userColumn, $filterUserId);
            }

            if ($aggregate === 'count') {
                $current = (clone $baseQuery)->whereBetween($dateColumn, [$currentMonthStart, $currentMonthEnd])->count();
                $previous = (clone $baseQuery)->whereBetween($dateColumn, [$previousMonthStart, $previousMonthEnd])->count();
            } else {
                $current = (clone $baseQuery)->whereBetween($dateColumn, [$currentMonthStart, $currentMonthEnd])->sum($aggregateColumn) ?? 0;
                $previous = (clone $baseQuery)->whereBetween($dateColumn, [$previousMonthStart, $previousMonthEnd])->sum($aggregateColumn) ?? 0;
            }

            return ['current' => $current, 'previous' => $previous];
        };

        $stats = [
            'zapytania' => $statsQuery(Zapytania::class, 'created_at', 'count'),
            'oferty' => $statsQuery(Oferta::class, 'created_at', 'count'),
            'klienci' => $statsQuery(Client::class, 'created_at', 'count'),
            'wartoscOfert' => $statsQuery(Oferta::class, 'created_at', 'sum', 'kwotaPLN'),
        ];

        // Fetch Zapytania
        $zapytanias = Zapytania::with(['user', 'opracowuje', 'client'])
            ->where(function ($query) {
                $query->whereNull('wznowienie')
                    ->orWhere('wznowienie', 0)
                    ->orWhere('wznowienie', 1); // Added this line to include wznowienie = 1
            })
            ->whereDoesntHave('oferty', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($filterUserId, function($query) use ($filterUserId) {
                $query->where('user_opracowuje_id', $filterUserId);
            })
            ->filter(['search' => Request::input('search', '')])
            ->get();

        // Fetch ZapytaniaWznowienie
        $zapytaniaWznowienie = ZapytaniaWznowienie::with(['user', 'opracowuje', 'zapytanie.client', 'zapytanie'])
            ->whereHas('zapytanie', function ($query) {
                $query->where('wznowienie', 2);
            })
            // Exclude ZapytaniaWznowienie if there's an offer for its parent Zapytania
            // that was created AFTER this specific ZapytaniaWznowienie.
            ->leftJoin('ofertas', function ($join) {
                $join->on('ofertas.zapytania_id', '=', 'zapytania_wznowienias.id_zapytania') // Corrected
                ->whereNull('ofertas.deleted_at') // Consider only active offers
                ->whereColumn('ofertas.created_at', '>', 'zapytania_wznowienias.time'); // Corrected
            })
            ->whereNull('ofertas.id') // Only include wznowienia that do NOT have such an offer
            ->when($filterUserId, function($query) use ($filterUserId) {
                $query->where('zapytania_wznowienias.user_opracowuje_id', $filterUserId);
            })
            ->filter(['search' => Request::input('search', '')])
            ->select('zapytania_wznowienias.*')
            ->get();

        // Map Zapytania to a consistent structure
        $combinedZapytania = $zapytanias->map(fn ($zapytania) => [
            'id' => $zapytania->id,
            'id_zapyt' => $zapytania->id_zapyt,
            'nazwa_projektu' => $zapytania->nazwa_projektu,
            'client' => $zapytania->client ? $zapytania->client : null,
            'data_zlozenia' => $zapytania->data_zlozenia ? $zapytania->data_zlozenia->format('Y-m-d') : null,
            'opracowuje' => $zapytania->opracowuje ? $zapytania->opracowuje : null,
            'wznowienie' => $zapytania->wznowienie,
            'user' => $zapytania->user ? $zapytania->user : null,
            'created_at' => $zapytania->created_at->format('Y-m-d H:i:s'),
            'type' => 'zapytanie', // Add a type to distinguish
            'link' => "/zapytania/{$zapytania->id}/edit", // Direct link for base zapytanie
        ]);

        // Map ZapytaniaWznowienie to the same consistent structure
        $combinedZapytaniaWznowienie = $zapytaniaWznowienie->map(fn ($wznowienie) => [
            'id' => $wznowienie->id,
            'wznowienie_id' => $wznowienie->id,
            'id_zapyt' => $wznowienie->zapytanie ? $wznowienie->zapytanie->id_zapyt : null, // Reference base zapytanie's id_zapyt
            'nazwa_projektu' => $wznowienie->zapytanie ? $wznowienie->zapytanie->nazwa_projektu . ' (Wznowienie)' : 'Wznowienie', // Indicate it's a renewal
            'client' => $wznowienie->zapytanie && $wznowienie->zapytanie->client ? $wznowienie->zapytanie->client : null,
            'data_zlozenia' => $wznowienie->data_zlozenia ? $wznowienie->data_zlozenia->format('Y-m-d') : null,
            'opracowuje' => $wznowienie->opracowuje ? $wznowienie->opracowuje : null,
            'wznowienie' => 2, // Mark as wznowienie=2 so frontend badge and button appear
            'user' => $wznowienie->user ? $wznowienie->user : null,
            'created_at' => $wznowienie->data_zlozenia ? $wznowienie->data_zlozenia->format('Y-m-d H:i:s') : null, // Use data_zlozenia for created_at if no timestamps
            'type' => 'wznowienie', // Add a type to distinguish
            'original_zapytanie_id' => $wznowienie->id_zapytania, // Keep reference to original
            'link' => "/zapytania/{$wznowienie->id_zapytania}/wznowienia/{$wznowienie->id}/edit", // Correct link for wznowienie
        ]);

        // Combine and sort all zapytania entries
        $finalZapytanias = $combinedZapytania->concat($combinedZapytaniaWznowienie)->sortBy('data_zlozenia')->values();


        return Inertia::render('Dashboard/Index',
            [
                'stats' => $stats,
                'filters' => Request::all('search', 'user_id', 'tab'),
                'users' => $isKierownictwo ? User::select('id', 'first_name', 'last_name')->orderBy('last_name')->get() : [],
                'historia' => Activity::with(['causer', 'subject'])
                    ->latest()
                    ->paginate(50)
                    ->withQueryString()
                    ->through(fn ($activity) => [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'subject_type' => str_replace('App\\Models\\', '', $activity->subject_type),
                        'subject_id' => $activity->subject_id,
                        'causer' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                        'properties' => $activity->properties,
                        'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                        'link' => $this->getLink($activity),
                    ]),
                'kontakts' => Kontakt::with(['client', 'kontaktperson', 'user', 'opiekun'])
                    ->where(function($query) {
                        // Pokazujemy kontakty zaplanowane na najbliższe 7 dni lub zaległe
                        $query->where('next_call_date', '<=', Carbon::today()->addDays(7))
                            ->orWhere('call_date', Carbon::today());
                    })
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where('opiekun_id', $filterUserId);
                    })
                    ->filter(Request::only('search'))
                    ->orderBy('next_call_date', 'asc')
                    ->orderBy('next_call_time', 'asc')
                    ->get()
                    ->map(fn ($kontakt) => [
                        'id' => $kontakt->id,
                        'client' => $kontakt->client ? $kontakt->client : null,
                        'kontaktperson' => $kontakt->kontaktperson ? $kontakt->kontaktperson : null,
                        'subject' => $kontakt->subject,
                        'next_call_date' => $kontakt->next_call_date ? $kontakt->next_call_date->format('Y-m-d') : null,
                        'next_call_time' => $kontakt->next_call_time,
                        'call_date' => $kontakt->call_date ? $kontakt->call_date->format('Y-m-d') : null,
                        'user' => $kontakt->user ? $kontakt->user : null,
                    ]),
                'zapytanias' => $finalZapytanias, // Use the combined and sorted collection
                'ofertas' => Oferta::with(['user', 'client', 'zapytania', 'ofertastatus'])
                    ->whereHas('zapytania', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->whereHas('ofertastatus', function ($query) {
                        $query->where('name', 'TOCZY');
                    })
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    })
                    ->filter(Request::only('search'))
                    ->orderBy('data_kontakt')
                    ->get()
                    ->map(fn ($oferta) => [
                        'id' => $oferta->id,
                        'nazwa_projektu' => $oferta->zapytania ? $oferta->zapytania->nazwa_projektu : 'Brak projektu',
                        'zapytania' => $oferta->zapytania ? $oferta->zapytania : null,
                        'client' => $oferta->client ? $oferta->client : null,
                        'data_kontakt' => $oferta->data_kontakt ? $oferta->data_kontakt->format('Y-m-d') : null,
                        'data_wyslania' => $oferta->data_wyslania ? $oferta->data_wyslania->format('Y-m-d') : null,
                        'status' => $oferta->ofertastatus ? $oferta->ofertastatus->name : null,
                        'user' => $oferta->user ? $oferta->user : null,
                        'created_at' => $oferta->created_at->format('Y-m-d H:i:s')
                    ]),
                'futureProjects' => FutureProject::with(['user', 'client', 'faza', 'kontakty' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where('user_id', $filterUserId);
                    })
                    ->filter(Request::only('search'))
                    ->orderBy('data_kontakt')
                    ->get()
                    ->map(function ($futureProject) {
                        $lastKontakt = $futureProject->kontakty->first();
                        return [
                            'id' => $futureProject->id,
                            'nazwa' => $futureProject->nazwa,
                            'client' => $futureProject->client ? $futureProject->client : null,
                            'data_kontakt' => ($lastKontakt && $lastKontakt->next_call_date) ? Carbon::parse($lastKontakt->next_call_date)->format('Y-m-d') : ($futureProject->data_kontakt ? $futureProject->data_kontakt->format('Y-m-d') : null),
                            'data_start' => $futureProject->data_start ? $futureProject->data_start->format('Y-m-d') : null,
                            'data_end' => $futureProject->data_end ? $futureProject->data_end->format('Y-m-d') : null,
                            'faza' => $futureProject->faza ? $futureProject->faza->name : null,
                            'user' => $futureProject->user ? $futureProject->user : null,
                        ];
                    }),
                'zadania' => Zadania::with('responsiblePerson')
                    ->with('user')
                    ->where('status', '!=', 'zamkniete')
                    ->where(function ($query) {
                        $query->whereNull('deadline')
                            ->orWhere('deadline', '<=', Carbon::today()->addDays(7));
                    })
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where('responsible_person_id', $filterUserId);
                    })
                    ->filter(Request::only('search'))
                    ->orderBy('deadline')
                    ->get()
                    ->map(fn ($zadanie) => [
                        'id' => $zadanie->id,
                        'subject' => $zadanie->subject,
                        'status' => $zadanie->status,
                        'deadline' => $zadanie->deadline ? $zadanie->deadline->format('Y-m-d') : null,
                        'users' => $zadanie->user ? $zadanie->user : null,
                    ]),
            ]);
    }

    private function getLink($activity)
    {
        $type = strtolower(str_replace('App\\Models\\', '', $activity->subject_type));

        $map = [
            'client' => 'clients',
            'zapytania' => 'zapytania',
            'oferta' => 'oferta',
            // The activity log for ZapytaniaWznowienie might need a different link structure
            // depending on how it's logged. For now, we'll keep it as a generic route.
            'zapytaniawznowienie' => 'zapytania-wznowienie',
        ];

        $route = $map[$type] ?? null;

        if ($route && $activity->subject_id) {
            return "/{$route}/{$activity->subject_id}/edit";
        }

        return null;
    }
}
