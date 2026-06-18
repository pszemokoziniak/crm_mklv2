<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ArchiveExistingClosedZadania extends Migration
{
    /**
     * Wszystkie istniejace zadania ze statusem 'zamkniete', ktore nie sa
     * jeszcze w archiwum, zostaja zarchiwizowane (deleted_at = closed_at
     * lub now() jesli closed_at brak).
     *
     * Od teraz zamykanie zadania automatycznie soft-deletuje (hook w modelu),
     * ta migracja czysci "zaleglosc".
     */
    public function up()
    {
        $now = now();

        $affected = DB::table('zadanias')
            ->where('status', 'zamkniete')
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => DB::raw('COALESCE(closed_at, "' . $now->toDateTimeString() . '")'),
            ]);

        // Informacyjny log do laravel.log (latwiej zobaczyc co sie stalo)
        \Illuminate\Support\Facades\Log::info('Backfill: zarchiwizowano zamkniete zadania', [
            'count' => $affected,
        ]);
    }

    public function down()
    {
        // Nie restore'ujemy automatycznie - nie sposob bezpiecznie odroznic
        // tych ktore byly soft-deleted z innego powodu od tych z tej migracji.
    }
}
