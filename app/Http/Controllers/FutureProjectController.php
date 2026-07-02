<?php

namespace App\Http\Controllers;

use App\Http\Requests\FutureProjectRequest;
use App\Http\Requests\ZapytaniaStoreRequest;
use App\Models\Branza;
use App\Models\Client;
use App\Models\Faza;
use App\Models\FutureProject;
use App\Models\Kraj;
use App\Models\Objekt;
use App\Models\User;
use App\Models\Zakres;
use App\Models\Zapytania;
use App\Models\Kontakt;
use Carbon\Carbon;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Traits\StoreActivityLog;
use Illuminate\Support\Facades\DB; // Dodaj to

class FutureProjectController extends Controller
{
    use StoreActivityLog;
    public function index()
    {
        $sortField = Request::input('field');
        $sortDirection = Request::input('direction') === 'asc' ? 'asc' : 'desc';

        $query = FutureProject::with('client')
            ->with('user')
            ->with('opiekun')
            ->with('kraj')
            ->with('faza')
            ->with('objekt')
            ->filter(Request::only('search', 'trashed'));

        if ($sortField === 'nazwa') {
            $query->orderBy('nazwa', $sortDirection);
        } elseif ($sortField === 'client') {
            $query->leftJoin('clients', 'future_projects.client_id', '=', 'clients.id')
                ->orderBy('clients.nazwa', $sortDirection)
                ->select('future_projects.*');
        } elseif ($sortField === 'kraj') {
            $query->leftJoin('krajs', 'future_projects.kraj_id', '=', 'krajs.id')
                ->orderBy('krajs.name', $sortDirection)
                ->select('future_projects.*');
        } elseif ($sortField === 'objekt') {
            $query->leftJoin('objekts', 'future_projects.objekt_id', '=', 'objekts.id')
                ->orderBy('objekts.name', $sortDirection)
                ->select('future_projects.*');
        } elseif ($sortField === 'faza') {
            $query->leftJoin('fazas', 'future_projects.faza_id', '=', 'fazas.id')
                ->orderBy('fazas.name', $sortDirection)
                ->select('future_projects.*');
        } else {
            $query->OrderByCreatedAt();
        }

        return Inertia::render('FutureProjects/Index', [
            'filters' => Request::all('search', 'trashed', 'field', 'direction'),
            'futureprojects' => $query
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($futureproject) => [
                    'id' => $futureproject->id,
                    'nazwa' => $futureproject->nazwa,
                    'miasto' => $futureproject->miasto,
                    'start' => $futureproject->start,
                    'end' => $futureproject->end,
                    'client' => $futureproject->client ? $futureproject->client : null,
                    'kraj' => $futureproject->kraj ? $futureproject->kraj : null,
                    'faza' => $futureproject->faza ? $futureproject->faza : null,
                    'objekt' => $futureproject->objekt ? $futureproject->objekt : null,
                    'user' => $futureproject->user ? $futureproject->user : null,
                    'opiekun' => $futureproject->opiekun ? $futureproject->opiekun : null,
                    'kwota' => $futureproject->kwota,

                    'deleted_at' => $futureproject->deleted_at,
                    'created_at' => date($futureproject->created_at)
                ])
        ]);
    }

    public function create()
    {

        return Inertia::render('FutureProjects/Create', [
            'objekt' => Objekt::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'faza' => Faza::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'kraj' => Kraj::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name', 'waluta'),
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::orderBy(DB::raw('TRIM(nazwa)'))->get()->map->only('id', 'nazwa'),
        ]);
    }
    public function store(FutureProjectRequest $request)
    {
        $data = FutureProject::create(array_merge($request->all(), ['user_id' => Auth::id()]));

        $this->storeActivityLog('Dodano przyszły projekt', $data->id, $request->client_id, 'futureproject', 'zmiany', Auth::id());

        return Redirect::route('futureproject')->with('success', 'Zapisano.');
    }

    public function edit(FutureProject $futureProject)
    {
        return Inertia::render('FutureProjects/Edit', [
            'futureproject' => [
                'id' => $futureProject->id,
                'nazwa' => $futureProject->nazwa,
                'miasto' => $futureProject->miasto,
                'kraj_id' => $futureProject->kraj_id,
                'objekt_id' => $futureProject->objekt_id,
                'client_id' => $futureProject->client_id,
                'start' => $futureProject->start,
                'end' => $futureProject->end,
                'opis' => $futureProject->opis,
                'inwestor' => $futureProject->inwestor,
                'dane_kontaktowe' => $futureProject->dane_kontaktowe,
                'data_kontakt' => $futureProject->data_kontakt,
                'faza_id' => $futureProject->faza_id,
                'user_id' => $futureProject->user_id,
                'opiekun_id' => $futureProject->opiekun_id,
                'kwota' => $futureProject->kwota,
                'deleted_at' => $futureProject->deleted_at,
            ],
            'objekt' => Objekt::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'faza' => Faza::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'krajs' => Kraj::orderBy(DB::raw('TRIM(name)'))->get()->map->only('id', 'name'),
            'users' => User::orderBy(DB::raw('TRIM(last_name)'))->orderBy(DB::raw('TRIM(first_name)'))->get()->map->only('id', 'first_name', 'last_name'),
            'clients' => Client::withTrashed()->orderBy(DB::raw('TRIM(nazwa)'))->get()->map->only('id', 'nazwa'),
            'kontakty' => Kontakt::with(['user', 'opiekun', 'kontaktperson', 'children.user', 'children.opiekun', 'children.kontaktperson'])
                ->where('future_project_id', $futureProject->id)
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($kontakt) => [
                    'id' => $kontakt->id,
                    'subject' => $kontakt->subject,
                    'description' => $kontakt->description,
                    'call_date' => $kontakt->call_date ? $kontakt->call_date->format('Y-m-d') : null,
                    'call_time' => $kontakt->call_time,
                    'user' => $kontakt->user,
                    'opiekun' => $kontakt->opiekun,
                    'kontaktperson' => $kontakt->kontaktperson,
                    'children' => $kontakt->children->map(fn ($reply) => [
                        'id' => $reply->id,
                        'subject' => $reply->subject,
                        'description' => $reply->description,
                        'call_date' => $reply->call_date ? $reply->call_date->format('Y-m-d') : null,
                        'call_time' => $reply->call_time,
                        'user' => $reply->user,
                        'opiekun' => $reply->opiekun,
                        'kontaktperson' => $reply->kontaktperson,
                    ]),
                ]),
            'activities' => $futureProject->activities()->with('causer')->latest()->get()->map(fn ($activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'user' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                'changes' => $activity->changes,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function update(FutureProject $futureProject, FutureProjectRequest $request)
    {
        $futureProject->update($request->all());

        $this->storeActivityLog('Poprawiono przyszły projekt', $futureProject->id, $request->client_id, 'futureproject', 'zmiany', Auth::id());

        return Redirect::back()->with('success', 'Zapytanie poprawione.');
    }

    public function destroy(FutureProject $futureProject)
    {
        $futureProject->delete();

        return Redirect::back()->with('success', 'Zapytanie usunięte.');
    }

    public function restore(FutureProject $futureProject)
    {
        $futureProject->restore();

        return Redirect::back()->with('success', 'Zapytanie przywrócone');
    }
}
