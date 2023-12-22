<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Linkedin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LinkedinController extends Controller
{
    public function index()
    {
        return Inertia::render('Linkedin/Index', [
            'linkedin' => Linkedin::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Linkedin/Create');
    }

    public function store(EditRequest $request)
    {
//        Linkedin::create($request->all());
        $data = new Linkedin();
        $data->name = $request->name;
        $data->link = $request->link;
        $data->save();

        return Redirect::route('linkedin')->with('success', 'Linkedin dodana.');
    }

    public function edit(Linkedin $linkedin)
    {
        return Inertia::render('Linkedin/Edit', [
            'linkedin' => [
                'id' => $linkedin->id,
                'name' => $linkedin->name,
                'link' => $linkedin->link,
                'checked_at' => $linkedin->checked_at,
            ],
        ]);
    }

    public function update(EditRequest $request)
    {
        Linkedin::find($request->id)->update([
            'name' => $request->name,
            'link' => $request->link
            ]);

        return Redirect::route('linkedin')->with('success', 'Poprawione.');
    }

    public function destroy(Linkedin $linkedin)
    {
        $linkedin->delete();

        return Redirect::route('linkedin')->with('success', 'Usunięte.');
    }
    public function click(Linkedin $linkedin)
    {
        $click = ($linkedin->click)+1;
        Linkedin::find($linkedin->id)->update([
            'click' => $click
        ]);

        return Redirect::to('https://www.pakainfo.com');
//        return Redirect::to($linkedin->link)->with('success', 'Usunięte.');
    }

    public function restore(Linkedin $linkedin)
    {
        $linkedin->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
