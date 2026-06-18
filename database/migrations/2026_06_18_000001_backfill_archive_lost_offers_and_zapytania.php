<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillArchiveLostOffersAndZapytania extends Migration
{
    /**
     * Backfill: wszystkie ofertas ze statusem przegrana/rezygnacja
     * ktore nie sa jeszcze zarchiwizowane -> deleted_at = now().
     * Plus ich parent zapytania (jesli jeszcze aktywne).
     *
     * Od teraz hook w modelu Oferta robi to automatycznie - ta migracja
     * domyka historie.
     */
    public function up()
    {
        $now = now()->toDateTimeString();

        // Pobierz id statusow terminalnych (case-insensitive)
        $terminalStatusIds = DB::table('oferta_statuses')
            ->whereRaw('LOWER(TRIM(name)) IN (?, ?)', ['przegrana', 'rezygnacja'])
            ->pluck('id')
            ->all();

        if (empty($terminalStatusIds)) {
            \Illuminate\Support\Facades\Log::warning('Backfill: brak statusow przegrana/rezygnacja, nic do roboty');
            return;
        }

        // Najpierw zbierz id zapytan ktore beda archiwizowane (zanim oferty zostana
        // miekko-skasowane - wtedy joiny moga ich nie zlapac w zaleznosci od scope'ow)
        $zapytaniaIds = DB::table('ofertas')
            ->whereIn('oferta_status_id', $terminalStatusIds)
            ->whereNull('deleted_at')
            ->whereNotNull('zapytania_id')
            ->pluck('zapytania_id')
            ->unique()
            ->all();

        // 1. Archiwizuj ofertas
        $ofertasArchived = DB::table('ofertas')
            ->whereIn('oferta_status_id', $terminalStatusIds)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => $now]);

        // 2. Archiwizuj parent zapytania (tylko jesli jeszcze nie sa)
        $zapytaniaArchived = 0;
        if (!empty($zapytaniaIds)) {
            $zapytaniaArchived = DB::table('zapytanias')
                ->whereIn('id', $zapytaniaIds)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now]);
        }

        \Illuminate\Support\Facades\Log::info('Backfill: archiwizacja zaleglosci ofert/zapytan', [
            'ofertas_archived' => $ofertasArchived,
            'zapytania_archived' => $zapytaniaArchived,
        ]);
    }

    public function down()
    {
        // Nie restore'ujemy - nie da sie bezpiecznie odroznic rekordow
        // zarchiwizowanych tej migracji od tych skasowanych z innego powodu.
    }
}
