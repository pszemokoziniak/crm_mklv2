<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->bigInteger('oferta_id')->unsigned()->nullable()->after('zapytania_id');
            $table->foreign('oferta_id')->references('id')->on('ofertas')->onDelete('set null');
            $table->index('oferta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontakts', function (Blueprint $table) {
            $table->dropForeign(['oferta_id']);
            $table->dropIndex(['oferta_id']);
            $table->dropColumn('oferta_id');
        });
    }
};
