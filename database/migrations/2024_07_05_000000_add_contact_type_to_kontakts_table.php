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
        Schema::table('kontakts', function (Blueprint $column) {
            $column->string('contact_type')->nullable()->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontakts', function (Blueprint $column) {
            $column->dropColumn('contact_type');
        });
    }
};
