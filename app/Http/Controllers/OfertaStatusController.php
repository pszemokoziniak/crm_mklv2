<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\OfertaStatus;
use App\Models\Zakres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OfertaStatusController extends Controller
{
    public function index()
    {
        return Inertia::render('OfertaStatus/Index', [
            'ofertaStatus' => OfertaStatus::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('OfertaStatus/Create');
    }

    public function store(EditRequest $request)
    {
        OfertaStatus::create($request->all());

        return Redirect::route('ofertastatus')->with('success', 'Status dodany.');
    }

    public function edit(OfertaStatus $ofertastatus)
    {
        return Inertia::render('OfertaStatus/Edit', [
            'ofertastatus' => [
                'id' => $ofertastatus->id,
                'name' => $ofertastatus->name,
                'deleted_at' => $ofertastatus->deleted_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        OfertaStatus::find($request->id)->update(
            ['name' => $request->name]
        );

        return Redirect::back()->with('success', 'Poprawiono.');
    }

    public function destroy(OfertaStatus $ofertastatus)
    {
        $ofertastatus->delete();

        return Redirect::route('ofertastatus')->with('success', 'Usunięto.');
    }

    public function restore(OfertaStatus $ofertastatus)
    {
        $ofertastatus->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
