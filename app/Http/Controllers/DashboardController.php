<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FutureProject;
use App\Models\Kontakt;
use App\Models\Oferta;
use App\Models\Zadania;
use App\Models\Zapytania;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Index',
            [
                'kontakts' => Kontakt::with('client')
                    ->with('kontaktperson')
                    ->with('user')
                    ->orderBy('call_time')
                    ->get(),
                'zapytanias' => Zapytania::with('user')
                    ->with('client')
//                    ->withTrashed()
                    ->orderBy('data_zlozenia')
                    ->get(),
                'ofertas' => Oferta::with('user')
                    ->with('client')
                    ->with('zapytania')
                    ->paginate(10)
                    ->withQueryString()
//                    ->withTrashed()
                    ->through(fn ($oferta) => [
                        'id' => $oferta->id,
                        'nazwa_projektu' => $oferta->nazwa_projektu,
                        'zapytania' => $oferta->zapytania ? $oferta->zapytania : null,
                        'client' => $oferta->client ? $oferta->client : null,
                        'data_kontakt' => $oferta->data_kontakt,
                        'user' => $oferta->user ? $oferta->user : null,
                        'created_at' => date($oferta->created_at)
                    ]),
                'futureProjects' => FutureProject::with('user')
                    ->with('client')
                    ->orderBy('data_kontakt')
                    ->get(),
                'zadania' => Zadania::with('users')
                    ->with('user')
                    ->orderBy('deadline')
                    ->get(),
            ]);
    }
}
