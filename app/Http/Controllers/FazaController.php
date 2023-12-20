<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Faza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FazaController extends Controller
{
    public function index()
    {
        return Inertia::render('Faza/Index', [
            'faza' => Faza::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Faza/Create');
    }

    public function store(EditRequest $request)
    {
        Faza::create($request->all());

        return Redirect::route('faza')->with('success', 'Faza dodana.');
    }

    public function edit(Faza $faza)
    {
        return Inertia::render('Faza/Edit', [
            'faza' => [
                'id' => $faza->id,
                'name' => $faza->name,
                'deleted_at' => $faza->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Faza::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::route('faza')->with('success', 'Poprawione.');
    }

    public function destroy(Faza $faza)
    {
        $faza->delete();

        return Redirect::route('faza')->with('success', 'Usunięte.');
    }

    public function restore(Faza $faza)
    {
        $faza->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
