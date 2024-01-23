<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\ContactStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;

class ClientController extends Controller
{
    use StoreActivityLog;
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'filters' => Request::all('search', 'trashed'),
            'clients' => Client::with('branza')
                ->with('user')
                ->with('kraj')
                ->orderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($client) => [
                    'id' => $client->id,
                    'nazwa' => $client->nazwa,
                    'ulica' => $client->ulica,
                    'miasto' => $client->miasto,
                    'kraj' => $client->kraj ? $client->kraj : null,
                    'www' => $client->www,
                    'linkedIn' => $client->linkedIn,
                    'branza' => $client->branza ? $client->branza : null,
                    'user' => $client->user ? $client->user : null,
                    'user_id' => $client->user_id,
                    'created_at' => date($client->created_at)
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create', [
            'branza' => Branza::get()->map->only('id', 'name'),
            'kraj' => Kraj::get()->map->only('id', 'name', 'waluta'),
        ]);
    }

    public function store(ClientRequest $request)
    {
        $data = Client::create($request->all());

        $this->storeActivityLog('Dodano klienta', $data->id, $data->id, 'clients', 'zmiany', Auth::id());

        return Redirect::route('clients')->with('success', 'Zapisano.');
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => [
                'id' => $client->id,
                'nazwa' => $client->nazwa,
                'ulica' => $client->ulica,
                'miasto' => $client->miasto,
                'www' => $client->www,
                'linkedIn' => $client->linkedIn,
                'waluta' => $client->waluta,
                'message' => $client->message,
                'branza_id' => $client->branza_id,
                'kraj_id' => $client->kraj_id,
                'user_id' => $client->user_id,
                'deleted_at' => $client->deleted_at,
            ],
            'branza' => Branza::get(),
            'kraj' => Kraj::get(),
            'user' => User::get(),
            'client_id' => $client->id,
        ]);
    }

    public function update(Client $client, ContactStoreRequest $request)
    {
        $client->update($request->validated());

        $this->storeActivityLog('Poprawiono klienta', $client->id, $client->id, 'clients', 'zmiany', Auth::id());

        return Redirect::back()->with('success', 'Klient poprawiony.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return Redirect::back()->with('success', 'Klient usunięty.');
    }

    public function restore(Client $client)
    {
        $client->restore();

        return Redirect::back()->with('success', 'Klient przywrócony');
    }
}
