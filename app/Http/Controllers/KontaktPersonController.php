<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class KontaktPersonController extends Controller
{
    public function index($client_id)
    {
        return Inertia::render('KontaktPerson/Index', [
            'kontaktPerson' => KontaktPerson::with('user')
                ->where('client_id', $client_id)
                ->get(),
            'client_id' => $client_id,
            'client' => Client::where('id', $client_id)->get()->map->only('nazwa'),
        ]);
    }
    public function create(Client $client)
    {
        return Inertia::render('KontaktPerson/Create', [
            'clients' => Client::get(),
            'client_id' => $client->id,
        ]);
    }
    public function store(Request $request)
    {
        KontaktPerson::create($request->all());

        return redirect()->route('kontaktperson', [$request->client_id])->with('success', 'Osoba kontaktowa dodana.');
    }
    public function edit(KontaktPerson $kontaktPerson)
    {
        return Inertia::render('KontaktPerson/Edit', [
            'kontaktPerson' => [
                'id' => $kontaktPerson->id,
                'first_name' => $kontaktPerson->first_name,
                'last_name' => $kontaktPerson->last_name,
                'position' => $kontaktPerson->position,
                'phone1' => $kontaktPerson->phone1,
                'phone2' => $kontaktPerson->phone2,
                'email' => $kontaktPerson->email,
                'client_id' => $kontaktPerson->client_id,
                'description' => $kontaktPerson->description,
                'user_id' => $kontaktPerson->user_id,
                'deleted_at' => $kontaktPerson->deleted_at,
            ],
        ]);
    }

    public function update(KontaktPerson $kontaktPerson, Request $request)
    {
        $kontaktPerson->update($request->all());

        return Redirect::route('kontaktperson', $kontaktPerson->client_id)->with('success', 'Poprawione.');
    }

    public function destroy(KontaktPerson $kontaktPerson)
    {
        $kontaktPerson->delete();

        return Redirect::route('kontaktperson', $kontaktPerson->client_id)->with('success', 'Usunięte.');
    }

    public function restore(KontaktPerson $faza)
    {
        $faza->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
