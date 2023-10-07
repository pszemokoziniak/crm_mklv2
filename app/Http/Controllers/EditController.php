<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EditController extends Controller
{
    public function index()
    {
        return Inertia::render('Edit/Index', [
//            'filters' => Request::all('search', 'trashed'),
//            'contacts' => Auth::user()->account->contacts()
//                ->with('organization')
//                ->orderByName()
//                ->filter(Request::only('search', 'trashed'))
//                ->paginate(10)
//                ->withQueryString()
//                ->through(fn ($contact) => [
//                    'id' => $contact->id,
//                    'name' => $contact->name,
//                    'phone' => $contact->phone,
//                    'city' => $contact->city,
//                    'deleted_at' => $contact->deleted_at,
//                    'organization' => $contact->organization ? $contact->organization->only('name') : null,
//                ]),
        ]);
    }
}
