<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZgloszenieFilesTable extends Migration
{
    public function up()
    {
        Schema::create('zgloszenie_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zgloszenie_id')->index();
            // Załącznik dodany w komentarzu — pokazujemy go pod tym komentarzem.
            $table->unsignedBigInteger('note_id')->nullable()->index();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('zgloszenie_id')->references('id')->on('zgloszenia')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('zgloszenie_files');
    }
}
