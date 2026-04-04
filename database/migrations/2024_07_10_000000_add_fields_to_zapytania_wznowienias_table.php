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
        Schema::table('zapytania_wznowienias', function (Blueprint $table) {
            $table->date('data_otrzymania')->nullable()->after('id_user');
            $table->date('data_zlozenia')->nullable()->after('data_otrzymania');
            $table->string('preliminarz')->nullable()->after('data_zlozenia');
            $table->unsignedBigInteger('zakres_id')->nullable()->after('preliminarz');
            $table->unsignedBigInteger('user_opracowuje_id')->nullable()->after('zakres_id');
            $table->date('start')->nullable()->after('user_opracowuje_id');
            $table->date('end')->nullable()->after('start');
            $table->decimal('kwota', 15, 2)->nullable()->after('end');
            $table->unsignedBigInteger('waluta_id')->nullable()->after('kwota');

            $table->foreign('zakres_id')->references('id')->on('zakres')->onDelete('set null');
            $table->foreign('user_opracowuje_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('waluta_id')->references('id')->on('walutas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zapytania_wznowienias', function (Blueprint $table) {
            $table->dropForeign(['zakres_id']);
            $table->dropForeign(['user_opracowuje_id']);
            $table->dropForeign(['waluta_id']);

            $table->dropColumn([
                'data_otrzymania',
                'data_zlozenia',
                'preliminarz',
                'zakres_id',
                'user_opracowuje_id',
                'start',
                'end',
                'kwota',
                'waluta_id',
            ]);
        });
    }
};
