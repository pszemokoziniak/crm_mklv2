<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\FutureProject;
use App\Models\Kontakt;
use App\Models\Oferta;
use App\Models\Zadania;
use App\Models\Zapytania;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Index',
            [
                'filters' => Request::all('search'),
                'historia' => ActivityLog::with('client')
                    ->with('user')
                    ->filter(Request::only('search'))
                    ->OrderByCreatedAt()
                    ->paginate(10)
                    ->withQueryString()
                    ->through(fn ($historia) => [
                        'id' => $historia->id,
                        'action' => $historia->action,
                        'link_id' => $historia->link_id ? $historia->link_id : null,
                        'client' => $historia->client ? $historia->client : null,
                        'link_action' => $historia->link_action,
                        'changes' => $historia->changes,
                        'user' => $historia->user ? $historia->user : null,
                        'deleted_at' => $historia->deleted_at,
                        'created_at' => date($historia->created_at)
                    ]),
                'kontakts' => Kontakt::with('client')
                    ->with('kontaktperson')
                    ->with('user')
                    ->filter(Request::only('search'))
                    ->orderBy('call_time')
                    ->get(),
                'zapytanias' => Zapytania::with(['user', 'opracowuje', 'client'])
                    ->where(function ($query) {
                        $query->whereNull('wznowienie')
                            ->orWhere('wznowienie', 0)
                            ->orWhere('wznowienie', 2);
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
                        'data_zlozenia' => $zapytania->data_zlozenia,
                        'opracowuje' => $zapytania->opracowuje ? $zapytania->opracowuje : null,
                        'wznowienie' => $zapytania->wznowienie,
                        'user' => $zapytania->user ? $zapytania->user : null,
                        'created_at' => date($zapytania->created_at)
                    ]),
                'ofertas' => Oferta::with(['user', 'client', 'zapytania', 'ofertastatus'])
                    ->filter(Request::only('search'))
                    ->orderBy('data_kontakt')
                    ->get()
                    ->map(fn ($oferta) => [
                        'id' => $oferta->id,
                        'nazwa_projektu' => $oferta->zapytania ? $oferta->zapytania->nazwa_projektu : 'Brak projektu',
                        'zapytania' => $oferta->zapytania ? $oferta->zapytania : null,
                        'client' => $oferta->client ? $oferta->client : null,
                        'data_kontakt' => $oferta->data_kontakt,
                        'data_wyslania' => $oferta->data_wyslania,
                        'status' => $oferta->ofertastatus ? $oferta->ofertastatus->name : null,
                        'user' => $oferta->user ? $oferta->user : null,
                        'created_at' => date($oferta->created_at)
                    ]),
                'futureProjects' => FutureProject::with('user')
                    ->with('client')
                    ->filter(Request::only('search'))
                    ->orderBy('data_kontakt')
                    ->get(),
                'zadania' => Zadania::with('users')
                    ->with('user')
                    ->filter(Request::only('search'))
                    ->orderBy('deadline')
                    ->get(),
            ]);
    }
}
