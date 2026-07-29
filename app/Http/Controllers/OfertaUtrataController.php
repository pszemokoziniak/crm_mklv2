<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class OfertaUtrataController extends Controller
{
    public function store(Oferta $oferta)
    {
        $data = Request::validate([
            'powod_utraty_id' => ['required', 'exists:powody_utraty,id'],
            'powod_utraty_dodatkowy_id' => ['nullable', 'exists:powody_utraty,id'],
            'etap_utraty' => ['nullable', 'in:po_ofercie,po_negocjacjach,po_wizji_lokalnej,inny'],
            'konkurent' => ['nullable', 'string', 'max:200'],
            'cena_konkurenta' => ['nullable', 'numeric'],
            'waluta_id' => ['nullable', 'exists:walutas,id'],
            'szansa_na_renegocjacje' => ['boolean'],
            'notatka' => ['nullable', 'string', 'max:5000'],
        ]);

        $oferta->utrataDetail()->updateOrCreate(
            ['oferta_id' => $oferta->id],
            array_merge($data, ['user_id' => Auth::id()])
        );

        return Redirect::back()->with('success', 'Zapisano powód utraty oferty.');
    }
}
