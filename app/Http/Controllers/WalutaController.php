<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\WalutaStoreRequest;
use App\Models\Waluta;
use App\Models\Zakres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class WalutaController extends Controller
{
    public function index()
    {
        return Inertia::render('Waluta/Index', [
            'waluta' => Waluta::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Waluta/Create');
    }

    public function store(WalutaStoreRequest $request)
    {
        Waluta::create($request->all());

        return Redirect::route('waluta')->with('success', 'Waluta dodana.');
    }

    public function edit(Waluta $waluta)
    {
        return Inertia::render('Waluta/Edit', [
            'waluta' => [
                'id' => $waluta->id,
                'name' => $waluta->name,
                'deleted_at' => $waluta->deleted_at,
            ],
        ]);
    }

    public function update(Waluta $waluta, Request $request)
    {
//        dd($waluta);
        $waluta->update($request->all());
//        Waluta::find($request->id)->update(
//            ['name' => $request->name]
//        );

        return Redirect::route('waluta')->with('success', 'Poprawiono.');
    }

    public function destroy(Waluta $waluta)
    {
        $waluta->delete();

        return Redirect::route('waluta')->with('success', 'Usunięto.');
    }

    public function restore(Waluta $waluta)
    {
        $waluta->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
