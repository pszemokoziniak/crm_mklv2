<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FutureProject;
use App\Models\Kontakt;
use App\Models\Oferta;
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
                    ->orderBy('data_zlozenia')
                    ->get(),
                'ofertas' => Oferta::with('user')
                    ->with('client')
                    ->with('zapytania')
                    ->orderBy('data_kontakt')
                    ->get(),
                'futureProjects' => FutureProject::with('user')
                    ->with('client')
                    ->orderBy('data_kontakt')
                    ->get(),
            ]);
    }
}
