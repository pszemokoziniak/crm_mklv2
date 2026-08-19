<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Lista klientów do wyszukiwarki w HRM.
     * Zwraca minimalny, stabilny kontrakt — HRM nie zna wewnętrznego schematu.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $clients = Client::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nazwa', 'like', '%'.$q.'%')
                    ->orWhere('miasto', 'like', '%'.$q.'%');
            })
            ->orderBy('nazwa')
            ->limit(20)
            ->get(['id', 'nazwa', 'miasto']);

        return response()->json(
            $clients->map(fn ($c) => [
                'id' => $c->id,
                'nazwa' => $c->nazwa,
                'miasto' => $c->miasto,
            ])
        );
    }
}
