<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Http\Requests\KursyStoreRequest;
use App\Models\Kraj;
use App\Models\Kursy;
use App\Models\Waluta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class KursyController extends Controller
{
    public function index()
    {
        return Inertia::render('Kursy/Index', [
            'kursy' => Kursy::with('waluta')->OrderByCreatedAt()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Kursy/Create', [
            'waluta' => Waluta::get()->map->only('id', 'name'),
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
                'waluta_id' => $kursy->waluta_id,
                'kurs' => $kursy->kurs,
                'deleted_at' => $kursy->deleted_at,
            ],
            'waluta' => Waluta::get(),
        ]);
    }

    public function update(KursyStoreRequest $request)
    {
        Kursy::find($request->id)->update(
            ['waluta_id' => $request->waluta_id, 'kurs' => $request->kurs],
        );

        return Redirect::route('kursy')->with('success', 'Poprawiono.');
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
