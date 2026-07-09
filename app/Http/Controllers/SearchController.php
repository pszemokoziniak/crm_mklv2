<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Kontakt;
use App\Models\KontaktPerson;
use App\Models\Oferta;
use App\Models\Zapytania;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Global search - zwraca do 5 wynikow per kategoria.
     * Uzywane przez GlobalSearch component (Ctrl+K).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        if (!Auth::check()) {
            return response()->json(['results' => []]);
        }

        $like = '%' . $q . '%';
        $limit = 5;

        // Klienci: po nazwie + z archiwum (admin czesto szuka historii)
        $clients = Client::withTrashed()
            ->where('nazwa', 'like', $like)
            ->orderByRaw('deleted_at IS NOT NULL ASC') // aktywne pierwsze
            ->orderBy('nazwa')
            ->limit($limit)
            ->get(['id', 'nazwa', 'deleted_at'])
            ->map(fn ($c) => [
                'type' => 'klient',
                'title' => $c->nazwa,
                'subtitle' => $c->deleted_at ? 'Archiwum' : null,
                'archived' => $c->deleted_at !== null,
                'link' => "/clients/{$c->id}/edit",
            ]);

        // Zapytania: po nazwie projektu lub id_zapyt
        $zapytania = Zapytania::withTrashed()
            ->with('client:id,nazwa')
            ->where(function ($sub) use ($like) {
                $sub->where('nazwa_projektu', 'like', $like)
                    ->orWhere('id_zapyt', 'like', $like);
            })
            ->orderByRaw('deleted_at IS NOT NULL ASC')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'nazwa_projektu', 'id_zapyt', 'client_id', 'deleted_at'])
            ->map(fn ($z) => [
                'type' => 'zapytanie',
                'title' => $z->nazwa_projektu ?: '(bez nazwy)',
                'subtitle' => ($z->id_zapyt ? $z->id_zapyt . ' · ' : '') . ($z->client?->nazwa ?: 'brak klienta'),
                'archived' => $z->deleted_at !== null,
                'link' => "/zapytania/{$z->id}/edit",
            ]);

        // Oferty: po nazwie projektu (przez zapytanie) lub id
        $ofertaLike = Oferta::withTrashed()
            ->with(['zapytania:id,nazwa_projektu', 'client:id,nazwa']);
        if (ctype_digit(ltrim($q, '#'))) {
            $ofertaLike->where('ofertas.id', (int) ltrim($q, '#'));
        } else {
            $ofertaLike->whereHas('zapytania', fn ($sq) => $sq->where('nazwa_projektu', 'like', $like));
        }
        $oferty = $ofertaLike
            ->orderByRaw('ofertas.deleted_at IS NOT NULL ASC')
            ->orderByDesc('ofertas.created_at')
            ->limit($limit)
            ->get(['ofertas.id', 'zapytania_id', 'client_id', 'ofertas.deleted_at'])
            ->map(fn ($o) => [
                'type' => 'oferta',
                'title' => 'Oferta #' . $o->id,
                'subtitle' => ($o->zapytania?->nazwa_projektu ?: 'brak projektu') . ' · ' . ($o->client?->nazwa ?: 'brak klienta'),
                'archived' => $o->deleted_at !== null,
                'link' => "/oferta/{$o->id}/edit",
            ]);

        // Osoby kontaktowe: po imieniu/nazwisku/emailu
        $osoby = KontaktPerson::with('client:id,nazwa')
            ->where(function ($sub) use ($like) {
                $sub->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderBy('last_name')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'email', 'client_id'])
            ->map(fn ($p) => [
                'type' => 'osoba',
                'title' => trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')) ?: '(bez imienia)',
                'subtitle' => ($p->email ?: '') . ($p->client ? ' · ' . $p->client->nazwa : ''),
                'archived' => false,
                'link' => "/kontaktperson/{$p->id}/edit",
            ]);

        // Kontakty (interakcje): po subject
        $kontakty = Kontakt::with('client:id,nazwa')
            ->where('subject', 'like', $like)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'subject', 'client_id', 'parent_id'])
            ->map(fn ($k) => [
                'type' => 'kontakt',
                'title' => $k->subject ?: '(bez tematu)',
                'subtitle' => $k->client?->nazwa ?: 'brak klienta',
                'archived' => false,
                'link' => '/kontakt/' . ($k->parent_id ?? $k->id) . '/edit',
            ]);

        return response()->json([
            'query' => $q,
            'groups' => array_values(array_filter([
                ['label' => 'Klienci', 'icon' => '🏢', 'items' => $clients->values()],
                ['label' => 'Zapytania', 'icon' => '📋', 'items' => $zapytania->values()],
                ['label' => 'Oferty', 'icon' => '📄', 'items' => $oferty->values()],
                ['label' => 'Osoby kontaktowe', 'icon' => '👤', 'items' => $osoby->values()],
                ['label' => 'Kontakty (rozmowy)', 'icon' => '💬', 'items' => $kontakty->values()],
            ], fn ($g) => $g['items']->count() > 0)),
        ]);
    }
}
