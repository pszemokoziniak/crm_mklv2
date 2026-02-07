<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditRequest;
use App\Models\Linkedin;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LinkedinController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Linkedin/Index', [
            'filters' => $request->all('search'),
            'linkedin' => Linkedin::with('client')
                ->when($request->input('search'), function ($query, $search) {
                    $query->whereHas('client', function ($q) use ($search) {
                        $q->where('nazwa', 'like', '%' . $search . '%');
                    });
                })
                ->orderByDesc('updated_at')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Linkedin/Create', [
            'clients' => Client::orderBy('nazwa')->get()->map->only('id', 'nazwa'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'link' => ['required', 'string'],
        ]);

        Linkedin::create([
            'client_id' => $request->client_id,
            'user_id' => auth()->id(),
            'link' => $request->link,
        ]);

        return Redirect::route('linkedin')->with('success', 'Linkedin dodana.');
    }

    public function edit(Linkedin $linkedin)
    {
        return Inertia::render('Linkedin/Edit', [
            'linkedin' => [
                'id' => $linkedin->id,
                'client_id' => $linkedin->client_id,
                'link' => $linkedin->link,
                'deleted_at' => $linkedin->deleted_at,
            ],
            'clients' => Client::orderBy('nazwa')->get()->map->only('id', 'nazwa'),
        ]);
    }

    public function update(Request $request, Linkedin $linkedin)
    {
        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'link' => ['required', 'string'],
        ]);

        $linkedin->update([
            'client_id' => $request->client_id,
            'link' => $request->link,
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
        $linkedin->increment('click');

        $link = $linkedin->link;
        if (!str_starts_with($link, 'http')) {
            $link = 'https://' . $link;
        }

        return Redirect::away($link);
    }

    public function restore(Linkedin $linkedin)
    {
        $linkedin->restore();

        return Redirect::back()->with('success', 'Przywrócono.');
    }
}
