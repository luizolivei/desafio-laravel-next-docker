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
        Schema::create('musics', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->unsignedBigInteger('album_id');
            $table->unsignedBigInteger('plataform_id'); // atualizado para seguir a convenção

            $table->string('isrc')->nullable();
            $table->string('trackId')->unique();
            $table->string('duration');
            $table->timestamp('addedDate')->nullable();
            $table->unsignedBigInteger('addedBy')->nullable();
            $table->string('url');
            $table->timestamps();

            $table->foreign('album_id')->references('id')->on('albuns')->onDelete('cascade');
            $table->foreign('plataform_id')->references('id')->on('plataforms')->onDelete('cascade');
            $table->foreign('addedBy')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musics');
    }
};
