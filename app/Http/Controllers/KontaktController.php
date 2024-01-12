<?php

namespace App\Http\Controllers;

use App\Models\Kontakt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KontaktController extends Controller
{
    public function index($clientId)
    {
        return Inertia::render('Kontakt/Index', [
            'kontakt' => Kontakt::with('user')
                ->where('client_id', $clientId)
                ->withQueryString()
                ->through(fn ($contact) => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'city' => $contact->city,
                    'deleted_at' => $contact->deleted_at,
                    'user_id' => $contact->user ? $contact->user->only('first_name', 'last_name') : null,
                ]),
        ]);
    }
}
