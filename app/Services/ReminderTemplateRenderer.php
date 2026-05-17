<?php

namespace App\Services;

use App\Models\FutureProject;
use App\Models\Oferta;
use App\Models\ReminderRule;
use App\Models\Zadania;
use App\Models\Zapytania;
use App\Models\ZapytaniaWznowienie;
use Carbon\Carbon;

class ReminderTemplateRenderer
{
    public function render(string $template, $subject, int $daysUntil): string
    {
        return strtr($template, $this->placeholders($subject, $daysUntil));
    }

    public function placeholders($subject, int $daysUntil): array
    {
        $client = $this->client($subject);
        $opiekun = $client && $client->creator ? $client->creator : null;
        $projekt = $this->projectName($subject);
        $date = $this->date($subject);

        return [
            '{{client_nazwa}}' => $client ? (string) $client->nazwa : '',
            '{{projekt_nazwa}}' => (string) $projekt,
            '{{data}}' => $date ? $date->format('Y-m-d') : '',
            '{{days_until}}' => (string) $daysUntil,
            '{{opiekun}}' => $opiekun ? trim($opiekun->first_name.' '.$opiekun->last_name) : '',
            '{{url}}' => $this->url($subject),
        ];
    }

    public static function availablePlaceholders(string $event): array
    {
        $base = ['{{client_nazwa}}', '{{projekt_nazwa}}', '{{data}}', '{{days_until}}', '{{opiekun}}', '{{url}}'];
        return $base;
    }

    private function client($subject)
    {
        if (!$subject) return null;
        if (isset($subject->client) && $subject->client) return $subject->client;
        if (isset($subject->zapytania) && $subject->zapytania && isset($subject->zapytania->client)) {
            return $subject->zapytania->client;
        }
        return null;
    }

    private function projectName($subject): string
    {
        if (!$subject) return '';
        if ($subject instanceof Oferta) {
            return $subject->zapytania ? (string) $subject->zapytania->nazwa_projektu : 'Oferta #'.$subject->id;
        }
        if ($subject instanceof Zapytania) {
            return (string) $subject->nazwa_projektu;
        }
        if ($subject instanceof ZapytaniaWznowienie) {
            return $subject->zapytania ? (string) $subject->zapytania->nazwa_projektu : 'Wznowienie #'.$subject->id;
        }
        if ($subject instanceof FutureProject) {
            return (string) ($subject->nazwa ?? 'Future #'.$subject->id);
        }
        if ($subject instanceof Zadania) {
            return (string) ($subject->subject ?? 'Zadanie #'.$subject->id);
        }
        return '';
    }

    private function date($subject): ?Carbon
    {
        if (!$subject) return null;
        if ($subject instanceof Oferta) {
            return $subject->data_kontakt ? Carbon::parse($subject->data_kontakt) : null;
        }
        if ($subject instanceof Zapytania) {
            return $subject->data_zlozenia ? Carbon::parse($subject->data_zlozenia) : null;
        }
        if ($subject instanceof ZapytaniaWznowienie) {
            return $subject->data_zlozenia ? Carbon::parse($subject->data_zlozenia) : null;
        }
        if ($subject instanceof FutureProject) {
            return $subject->data_start ? Carbon::parse($subject->data_start) : null;
        }
        if ($subject instanceof Zadania) {
            return $subject->deadline ? Carbon::parse($subject->deadline) : null;
        }
        return null;
    }

    private function url($subject): string
    {
        if (!$subject) return '';
        if ($subject instanceof Oferta) {
            return url("/oferta/{$subject->id}/edit");
        }
        if ($subject instanceof Zapytania) {
            return url("/zapytania/{$subject->id}/edit");
        }
        if ($subject instanceof ZapytaniaWznowienie) {
            $zapId = $subject->zapytania_id ?? ($subject->zapytania ? $subject->zapytania->id : null);
            return $zapId ? url("/zapytania/{$zapId}/wznowienia/{$subject->id}/edit") : '';
        }
        if ($subject instanceof FutureProject) {
            return url("/futureproject/{$subject->id}/edit");
        }
        if ($subject instanceof Zadania) {
            return url("/zadania/{$subject->id}/edit");
        }
        return '';
    }
}
