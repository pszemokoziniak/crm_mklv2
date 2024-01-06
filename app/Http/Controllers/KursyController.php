<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\KursyStoreRequest;
use App\Models\Kraj;
use App\Models\Kursy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KursyController extends Controller
{
    public function index()
    {
        return Inertia::render('Kursy/Index', [
            'kursy' => Kursy::OrderByCreatedAt()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Kursy/Create', [
            'waluta' => Kraj::get()->map->only('id', 'waluta'),
        ]);
    }

    public function store(KursyStoreRequest $request)
    {
        Kursy::create($request->all());

        return Redirect::route('kursy')->with('success', 'Kurs dodany.');
    }

    public function edit(Kursy $kursy)
    {
        return Inertia::render('Kursy/Edit', [
            'kursy' => [
                'id' => $kursy->id,
                'name' => $kursy->name,
                'kurs' => $kursy->kurs,
                'deleted_at' => $kursy->deleted_at,
            ],
        ]);
    }

    public function update(KursyStoreRequest $request)
    {
        Kursy::find($request->id)->update(
            ['name' => $request->name, 'kurs' => $request->kurs],
        );

        return Redirect::back()->with('success', 'Poprawiono.');
    }

//    public function destroy(Kraj $kraj)
//    {
//        $kraj->delete();
//
//        return Redirect::route('branza')->with('success', 'Usunięte.');
//    }
//
//    public function restore(Kraj $kraj)
//    {
//        $kraj->restore();
//
//        return Redirect::back()->with('success', 'Przywrócono.');
//    }
}
