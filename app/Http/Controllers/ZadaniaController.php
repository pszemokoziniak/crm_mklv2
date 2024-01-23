<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZadaniaStoreRequest;
use App\Models\User;
use App\Models\Zadania;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;

use App\Traits\StoreActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ZadaniaController extends Controller
{
    use StoreActivityLog;

    public function index()
    {
        return Inertia::render('Zadania/Index', [
            'filters' => Request::all('search', 'trashed'),
            'zadanias' => Zadania::with('user')
                ->with('users')
//                ->OrderByCreatedAt()
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($zadania) => [
                    'id' => $zadania->id,
                    'responsible_person_id' => $zadania->users ? $zadania->users : null,
                    'subject' => $zadania->subject,
                    'description' => $zadania->description,
                    'deadline' => $zadania->deadline,
                    'user' => $zadania->user ? $zadania->user : null,
                    'deleted_at' => $zadania->deleted_at,
                    'created_at' => date($zadania->created_at)
                ])
        ]);
    }
    public function create()
    {

        return Inertia::render('Zadania/Create', [
            'users' => User::get()->map->only('id', 'first_name', 'last_name'),
        ]);
    }
    public function store(ZadaniaStoreRequest $request)
    {
        Zadania::create($request->all());

//        $this->storeActivityLog('Nowe zadanie', $data->id, $request->responsible_person_id, 'oferta', 'zmiany', Auth::id());

        return Redirect::route('zadania')->with('success', 'Zapisano.');
    }

    public function edit(Zadania $zadania)
    {
        return Inertia::render('Zadania/Edit', [
            'zadanie' => [
                'id' => $zadania->id,
                'responsible_person_id' => $zadania->responsible_person_id,
                'subject' => $zadania->subject,
                'description' => $zadania->description,
                'deadline' => $zadania->deadline,
                'user_id' => $zadania->user_id,
                'deleted_at' => $zadania->deleted_at,
            ],
            'users' => User::get(),
        ]);
    }

    public function update(Zadania $zadania, ZadaniaStoreRequest $request)
    {
        try {
            $zadania->update($request->all());
//            $this->storeActivityLog('Poprawiono ofertę', $oferta->id, $request->client_id, 'oferta', 'zmiany', Auth::id());

            return Redirect::route('zadania')->with('success', 'Oferta poprawiona.');

        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function destroy(Zadania $zadania)
    {
        $zadania->delete();

        return Redirect::back()->with('success', 'Zadanie zarchiwizowane.');
    }

    public function restore(Zadania $zadania)
    {
        $zadania->restore();

        return Redirect::back()->with('success', 'Zadanie przywrócone');
    }
}
