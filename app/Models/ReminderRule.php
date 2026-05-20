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
    public const EVENT_ZADANIA_DEADLINE = 'zadania_deadline';
    public const EVENT_ZADANIA_UTWORZONE = 'zadania_utworzone';
    public const EVENT_ZAPYTANIE_UTWORZONE = 'zapytanie_utworzone';

    public const EVENTS = [
        self::EVENT_OFERTA_KONTAKT => 'Termin kontaktu z oferty',
        self::EVENT_ZAPYTANIE_TERMIN_ZLOZENIA => 'Termin złożenia oferty (zapytanie)',
        self::EVENT_WZNOWIENIE_TERMIN => 'Termin wznowienia zapytania',
        self::EVENT_FUTURE_PROJECT_START => 'Start fazy Future Project',
        self::EVENT_ZADANIA_DEADLINE => 'Termin zadania',
        self::EVENT_ZADANIA_UTWORZONE => 'Utworzenie nowego zadania',
        self::EVENT_ZAPYTANIE_UTWORZONE => 'Utworzenie nowego zapytania',
    ];

    public const IMMEDIATE_EVENTS = [
        self::EVENT_ZADANIA_UTWORZONE,
        self::EVENT_ZAPYTANIE_UTWORZONE,
    ];

    public const FILTER_PRELIMINARZ_ONLY = 'preliminarz_only';

    public const EVENT_FILTERS = [
        self::EVENT_ZAPYTANIE_UTWORZONE => [
            '' => 'Wszystkie nowe zapytania',
            self::FILTER_PRELIMINARZ_ONLY => 'Tylko z PRELIMINARZ = Tak',
        ],
    ];

    public static function isImmediateEvent(string $event): bool
    {
        return in_array($event, self::IMMEDIATE_EVENTS, true);
    }

    public function passesFilter($subject): bool
    {
        if (!$this->event_filter) {
            return true;
        }

        if ($this->event_filter === self::FILTER_PRELIMINARZ_ONLY && $subject instanceof Zapytania) {
            return ($subject->preliminarz ?? null) === 'Tak';
        }

        return true;
    }

    public const CHANNEL_MAIL = 'mail';
    public const CHANNEL_DATABASE = 'database';
    public const CHANNEL_WEBPUSH = 'webpush';

    public const CHANNELS = [
        self::CHANNEL_MAIL => 'Email',
        self::CHANNEL_WEBPUSH => 'Push (przeglądarka + dźwięk)',
        self::CHANNEL_DATABASE => 'Powiadomienie w aplikacji (dzwonek)',
    ];

    protected $fillable = [
        'name',
        'event',
        'event_filter',
        'days_before',
        'recipients',
        'channels',
        'subject',
        'body',
        'active',
    ];

    protected $casts = [
        'recipients' => 'array',
        'channels' => 'array',
        'active' => 'boolean',
        'days_before' => 'integer',
    ];

    public function getChannelsAttribute($value): array
    {
        $list = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($list) || empty($list)) {
            return [self::CHANNEL_MAIL];
        }
        return array_values(array_intersect($list, array_keys(self::CHANNELS)));
    }

    public function resolveRecipients($subject): Collection
    {
        $users = collect();
        $requiresEmail = in_array(self::CHANNEL_MAIL, $this->channels, true);

        foreach ((array) $this->recipients as $token) {
            $user = null;

            if ($token === 'opiekun') {
                $client = $this->subjectClient($subject);
                $user = $client ? $client->creator : null;
            } elseif ($token === 'opracowuje') {
                $user = $this->subjectOpracowujacy($subject);
            } elseif ($token === 'osoba_odpowiedzialna') {
                $user = $this->subjectResponsible($subject);
            } elseif (str_starts_with($token, 'user:')) {
                $id = (int) substr($token, 5);
                if ($id > 0) {
                    $user = User::find($id);
                }
            } elseif (str_starts_with($token, 'role:')) {
                $roleName = substr($token, 5);
                if ($roleName !== '') {
                    User::role($roleName)->get()->each(function ($u) use ($users, $requiresEmail) {
                        if (!$requiresEmail || $u->email) {
                            $users->put($u->id, $u);
                        }
                    });
                }
                continue;
            }

            if ($user && (!$requiresEmail || $user->email)) {
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
        if ($subject instanceof Zadania) {
            return $subject->user;
        }
        return null;
    }

    private function subjectResponsible($subject)
    {
        if ($subject instanceof Zadania) {
            return $subject->responsiblePerson ?: $subject->user;
        }
        return null;
    }
}
