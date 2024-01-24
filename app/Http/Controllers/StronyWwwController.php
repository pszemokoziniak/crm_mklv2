<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\StronyWww;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StronyWwwController extends Controller
{
    public function index()
    {
        return Inertia::render('StronyWww/Index', [
            'stronywww' => StronyWww::get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('StronyWww/Create');
    }

    public function store(EditRequest $request)
    {
//        StronyWww::create($request->all());
        $data = new StronyWww();
        $data->name = $request->name;
        $data->link = $request->link;
        $data->save();

        return Redirect::route('stronywww')->with('success', 'Strony WWW dodana.');
    }

    public function edit(StronyWww $stronyWww)
    {
        return Inertia::render('StronyWww/Edit', [
            'stronywww' => [
                'id' => $stronyWww->id,
                'name' => $stronyWww->name,
                'link' => $stronyWww->link,
                'checked_at' => $stronyWww->checked_at,
            ],
        ]);
    }

    public function update(Request $request)
    {
        StronyWww::find($request->id)->update([
            'name' => $request->name,
            'link' => $request->link
        ]);

        return Redirect::route('stronywww')->with('success', 'Poprawione.');
    }

    public function destroy(StronyWww $stronyWww)
    {
        $stronyWww->delete();

        return Redirect::route('stronywww')->with('success', 'Usunięte.');
    }
    public function click(StronyWww $stronyWww)
    {
        $click = ($stronyWww->click)+1;
        StronyWww::find($stronyWww->id)->update([
            'click' => $click
        ]);

        return Redirect::away((str_contains($stronyWww->link, 'https://'))?$stronyWww->link:'https://'.$stronyWww->link);
    }

    public function restore(StronyWww $stronyWww)
    {
        $stronyWww->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
