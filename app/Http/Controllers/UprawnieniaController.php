<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Uprawnienia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UprawnieniaController extends Controller
{
    public function index()
    {
        return Inertia::render('Uprawnienia/Index', [
            'uprawnienia' => Uprawnienia::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Uprawnienia/Create');
    }

    public function store(EditRequest $request)
    {
        Uprawnienia::create($request->all());

        return Redirect::route('uprawnienia')->with('success', 'Uprawnienia dodane.');
    }

    public function edit(Uprawnienia $uprawnienia)
    {
        return Inertia::render('Uprawnienia/Edit', [
            'uprawnienia' => [
                'id' => $uprawnienia->id,
                'name' => $uprawnienia->name,
                'deleted_at' => $uprawnienia->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Uprawnienia::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::route('uprawnienia')->with('success', 'Poprawiono.');
    }

    public function destroy(Uprawnienia $uprawnienia)
    {
        $uprawnienia->delete();

        return Redirect::route('uprawnienia')->with('success', 'Usunięto.');
    }

    public function restore(Uprawnienia $uprawnienia)
    {
        $uprawnienia->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
