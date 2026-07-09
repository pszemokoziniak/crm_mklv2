<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Oferta;
use App\Models\User;
use App\Models\Zapytania;
use App\Notifications\NoteMentionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NoteController extends Controller
{
    private const TYPE_MAP = [
        'zapytania' => Zapytania::class,
        'oferta' => Oferta::class,
    ];

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:zapytania,oferta'],
            'notable_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $modelClass = self::TYPE_MAP[$data['type']];
        // Weryfikujemy ze notable istnieje (nawet zarchiwizowane, admin moze notowac historie)
        $notable = $modelClass::withTrashed()->find($data['notable_id']);
        if (!$notable) {
            return back()->with('error', 'Nie znaleziono powiazanego rekordu.');
        }

        $note = Note::create([
            'user_id' => Auth::id(),
            'notable_type' => $modelClass,
            'notable_id' => $data['notable_id'],
            'body' => $data['body'],
        ]);

        $this->notifyMentioned($note);

        return back()->with('success', 'Notatka dodana.');
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return back()->with('error', 'Możesz edytować tylko swoje notatki.');
        }
        $data = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $oldBody = $note->body;
        $note->update(['body' => $data['body']]);

        // Powiadamiamy tylko o NOWO wspomnianych osobach (nie dublujemy notyfikacji)
        $oldMentions = $this->extractMentionedIds($oldBody);
        $newMentions = $this->extractMentionedIds($note->body);
        $freshMentions = array_diff($newMentions, $oldMentions);
        if (!empty($freshMentions)) {
            $this->sendMentionNotifications($note, $freshMentions);
        }

        return back()->with('success', 'Notatka poprawiona.');
    }

    public function destroy(Note $note)
    {
        $user = Auth::user();
        if ($note->user_id !== $user->id && !$user->hasAnyRole(['super-admin', 'Administrator'])) {
            return back()->with('error', 'Możesz usunąć tylko swoje notatki.');
        }
        $note->delete();
        return back()->with('success', 'Notatka usunięta.');
    }

    /**
     * Parsuje @Imie Nazwisko z body notatki i wysyla notyfikacje.
     */
    protected function notifyMentioned(Note $note): void
    {
        $ids = $this->extractMentionedIds($note->body);
        if (empty($ids)) {
            return;
        }
        $this->sendMentionNotifications($note, $ids);
    }

    protected function sendMentionNotifications(Note $note, array $userIds): void
    {
        // Filtrujemy siebie (nie wysylamy notyfikacji do autora)
        $userIds = array_values(array_filter($userIds, fn ($id) => (int) $id !== (int) Auth::id()));
        if (empty($userIds)) {
            return;
        }

        try {
            $recipients = User::whereIn('id', $userIds)->get();
            if ($recipients->isEmpty()) {
                return;
            }
            Notification::send($recipients, new NoteMentionNotification($note, Auth::user()));
        } catch (\Throwable $e) {
            Log::warning('Note mention notify failed: ' . $e->getMessage(), ['note_id' => $note->id]);
        }
    }

    /**
     * Wyciaga id userow wspomnianych w body.
     * Format wzmianki: @[Imie Nazwisko](user:ID) - stabilny wobec zmian nazwiska.
     * Tolerujemy tez zwykle @Imie Nazwisko (fallback) matchowane po first+last.
     */
    protected function extractMentionedIds(string $body): array
    {
        $ids = [];

        // 1. Format kanoniczny @[Imie Nazwisko](user:X)
        if (preg_match_all('/@\[[^\]]+\]\(user:(\d+)\)/u', $body, $m)) {
            foreach ($m[1] as $id) {
                $ids[] = (int) $id;
            }
        }

        // 2. Fallback: @Imie Nazwisko (2 slowa) - matchujemy po polach
        if (preg_match_all('/@([\p{L}]+)\s+([\p{L}]+)/u', $body, $m)) {
            $pairs = array_map(fn ($fn, $ln) => ['first' => $fn, 'last' => $ln], $m[1], $m[2]);
            foreach ($pairs as $p) {
                $u = User::where('first_name', $p['first'])->where('last_name', $p['last'])->first();
                if ($u) {
                    $ids[] = $u->id;
                }
            }
        }

        return array_values(array_unique($ids));
    }
}
