<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use App\Models\Client;
use App\Models\FutureProject;
use App\Models\Kontakt;
use App\Models\Oferta;
use App\Models\Zadania;
use App\Models\Zapytania;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        return Inertia::render('Dashboard/Index',
            [
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
                        $query->where(function($q) use ($filterUserId) {
                            $q->where('opiekun_id', $filterUserId)
                              ->orWhere('user_id', $filterUserId);
                        });
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
                'zapytanias' => Zapytania::with(['user', 'opracowuje', 'client'])
                    ->where(function ($query) {
                        $query->whereNull('wznowienie')
                            ->orWhere('wznowienie', 0)
                            ->orWhere('wznowienie', 2);
                    })
                    ->whereDoesntHave('oferty', function ($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where(function($q) use ($filterUserId) {
                            $q->where('user_opracowuje_id', $filterUserId)
                              ->orWhere('user_id', $filterUserId);
                        });
                    })
                    ->pendingOrOld()
                    ->filter(Request::only('search'))
                    ->orderBy('data_zlozenia')
                    ->get()
                    ->map(fn ($zapytania) => [
                        'id' => $zapytania->id,
                        'id_zapyt' => $zapytania->id_zapyt,
                        'nazwa_projektu' => $zapytania->nazwa_projektu,
                        'client' => $zapytania->client ? $zapytania->client : null,
                        'data_zlozenia' => $zapytania->data_zlozenia ? $zapytania->data_zlozenia->format('Y-m-d') : null,
                        'opracowuje' => $zapytania->opracowuje ? $zapytania->opracowuje : null,
                        'wznowienie' => $zapytania->wznowienie,
                        'user' => $zapytania->user ? $zapytania->user : null,
                        'created_at' => $zapytania->created_at->format('Y-m-d H:i:s')
                    ]),
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
                    ->where(function ($query) {
                        $query->whereNull('deadline')
                              ->orWhere('deadline', '<=', Carbon::today()->addDays(7));
                    })
                    ->when($filterUserId, function($query) use ($filterUserId) {
                        $query->where(function($q) use ($filterUserId) {
                            $q->where('responsible_person_id', $filterUserId)
                              ->orWhere('user_id', $filterUserId);
                        });
                    })
                    ->filter(Request::only('search'))
                    ->orderBy('deadline')
                    ->get()
                    ->map(fn ($zadanie) => [
                        'id' => $zadanie->id,
                        'subject' => $zadanie->subject,
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
        ];

        $route = $map[$type] ?? null;

        if ($route && $activity->subject_id) {
            return "/{$route}/{$activity->subject_id}/edit";
        }

        return null;
    }
}
