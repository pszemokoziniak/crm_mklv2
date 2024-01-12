<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Index',
            [
//                'clients' => Client::where()
            ]);
    }
}
