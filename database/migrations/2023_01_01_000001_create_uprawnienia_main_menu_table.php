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
        Schema::create('uprawnienia_main_menu', function (Blueprint $table) {
            // Zmieniono, aby odnosiło się do tabeli 'roles' pakietu Spatie
            $table->foreignId('uprawnienia_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('main_menu_id')->constrained()->onDelete('cascade');
            $table->primary(['uprawnienia_id', 'main_menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uprawnienia_main_menu');
    }
};
