<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\ContactStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Kraj;
use App\Models\User;
use App\Models\Kontakt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;
use Carbon\Carbon;

class ClientController extends Controller
{
    use StoreActivityLog;
    public function index()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);

        return Inertia::render('Clients/Index', [
            'filters' => Request::all('search', 'trashed', 'status'),
            'clients' => Client::with(['branza', 'user', 'kraj', 'creator'])
                ->withCount(['zapytania', 'oferty', 'kontakty'])
                ->withCount(['zapytania as recent_zapytania_count' => function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                }])
                ->withCount(['kontakty as recent_kontakty_count' => function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                }])
                ->withCount(['oferty as recent_oferty_count' => function ($query) use ($sixMonthsAgo) {
                    $query->where('created_at', '>=', $sixMonthsAgo);
                }])
                ->orderByCreatedAt()
                ->filter(Request::only('search', 'trashed', 'status'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($client) => [
                    'id' => $client->id,
                    'nazwa' => $client->nazwa,
                    'ulica' => $client->ulica,
                    'miasto' => $client->miasto,
                    'kraj' => $client->kraj ? $client->kraj->name : '-',
                    'www' => $client->www,
                    'linkedIn' => $client->linkedIn,
                    'branza' => $client->branza ? $client->branza->name : '-',
                    'user' => $client->user ? (trim($client->user->first_name) != 'N/A' ? $client->user->first_name . ' ' . $client->user->last_name : $client->user->first_name) : '-',
                    'user_id' => $client->user_id,
                    'created_by' => $client->creator ? $client->creator->first_name . ' ' . $client->creator->last_name : '-',
                    'created_at' => $client->created_at->format('Y-m-d H:i:s'),
                    'zapytania_count' => $client->zapytania_count,
                    'oferty_count' => $client->oferty_count,
                    'kontakty_count' => $client->kontakty_count,
                    'is_active' => ($client->recent_zapytania_count > 0 || $client->recent_kontakty_count > 0 || $client->recent_oferty_count > 0)
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
        $data = Client::create(array_merge($request->all(), ['created_by' => Auth::id()]));

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
            'zapytania' => $client->zapytania()->with(['oferty.status', 'oferty.waluta', 'user'])->orderBy('created_at', 'desc')->get()->map(fn ($zapytanie) => [
                'id' => $zapytanie->id,
                'nazwa_projektu' => $zapytanie->nazwa_projektu,
                'created_at' => $zapytanie->created_at->format('Y-m-d'),
                'user' => $zapytanie->user ? $zapytanie->user->first_name . ' ' . $zapytanie->user->last_name : '-',
                'oferty' => $zapytanie->oferty->map(fn ($oferta) => [
                    'id' => $oferta->id,
                    'numer_oferty' => $oferta->numer_oferty,
                    'status' => $oferta->status ? $oferta->status->name : '-',
                    'kwota' => $oferta->kwota,
                    'waluta' => $oferta->waluta ? $oferta->waluta->name : '',
                    'created_at' => $oferta->created_at->format('Y-m-d'),
                ]),
            ]),
            'kontakty' => Kontakt::with(['user', 'kontaktperson', 'children.user', 'children.kontaktperson'])
                ->where('client_id', $client->id)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->get(),
            'activities' => $client->activities()->with('causer')->latest()->get()->map(fn ($activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'user' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                'changes' => $activity->changes,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ]),
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
