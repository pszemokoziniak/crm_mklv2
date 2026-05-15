<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class ReminderRule extends Model
{
    use SoftDeletes;

    public const EVENT_OFERTA_KONTAKT = 'oferta_kontakt';
    public const EVENT_ZAPYTANIE_TERMIN_ZLOZENIA = 'zapytanie_termin_zlozenia';
    public const EVENT_WZNOWIENIE_TERMIN = 'wznowienie_termin';
    public const EVENT_FUTURE_PROJECT_START = 'future_project_start';

    public const EVENTS = [
        self::EVENT_OFERTA_KONTAKT => 'Termin kontaktu z oferty',
        self::EVENT_ZAPYTANIE_TERMIN_ZLOZENIA => 'Termin złożenia oferty (zapytanie)',
        self::EVENT_WZNOWIENIE_TERMIN => 'Termin wznowienia zapytania',
        self::EVENT_FUTURE_PROJECT_START => 'Start fazy Future Project',
    ];

    protected $fillable = [
        'name',
        'event',
        'days_before',
        'recipients',
        'subject',
        'body',
        'active',
    ];

    protected $casts = [
        'recipients' => 'array',
        'active' => 'boolean',
        'days_before' => 'integer',
    ];

    public function resolveRecipients($subject): Collection
    {
        $users = collect();

        foreach ((array) $this->recipients as $token) {
            $user = null;

            if ($token === 'opiekun') {
                $client = $this->subjectClient($subject);
                $user = $client ? $client->creator : null;
            } elseif ($token === 'opracowuje') {
                $user = $this->subjectOpracowujacy($subject);
            } elseif (str_starts_with($token, 'user:')) {
                $id = (int) substr($token, 5);
                if ($id > 0) {
                    $user = User::find($id);
                }
            }

            if ($user && $user->email) {
                $users->put($user->id, $user);
            }
        }

        return $users->values();
    }

    private function subjectClient($subject)
    {
        if (!$subject) {
            return null;
        }
        if (isset($subject->client) && $subject->client) {
            return $subject->client;
        }
        if (isset($subject->zapytania) && $subject->zapytania && isset($subject->zapytania->client)) {
            return $subject->zapytania->client;
        }
        return null;
    }

    private function subjectOpracowujacy($subject)
    {
        if (!$subject) {
            return null;
        }
        if ($subject instanceof Zapytania) {
            return $subject->opracowuje ?: $subject->user;
        }
        if ($subject instanceof Oferta) {
            return $subject->user;
        }
        if ($subject instanceof ZapytaniaWznowienie) {
            return $subject->user;
        }
        if ($subject instanceof FutureProject) {
            return $subject->user;
        }
        return null;
    }
}
