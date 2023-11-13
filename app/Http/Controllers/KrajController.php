<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Branza;
use App\Models\Kraj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KrajController extends Controller
{
    public function index()
    {
        return Inertia::render('Kraj/Index', [
            'krajs' => Kraj::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Kraj/Create');
    }

    public function store(EditRequest $request)
    {
        Kraj::create($request->all());

        return Redirect::route('kraj')->with('success', 'Kraj dodany.');
    }

    public function edit(Kraj $kraj)
    {
        return Inertia::render('Kraj/Edit', [
            'kraj' => [
                'id' => $kraj->id,
                'name' => $kraj->name,
                'waluta' => $kraj->waluta,
                'deleted_at' => $kraj->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Kraj::find($request->id)->update(
            ['name' => $request->name, 'waluta' => $request->waluta],
//            ['waluta' => $request->waluta],
        );

        return Redirect::back()->with('success', 'Poprawione.');
    }

    public function destroy(Kraj $kraj)
    {
        $kraj->delete();

        return Redirect::route('branza')->with('success', 'Usunięte.');
    }

    public function restore(Kraj $kraj)
    {
        $kraj->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
