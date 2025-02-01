<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('artist_music', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artist_id');
            $table->unsignedBigInteger('music_id');
            $table->timestamps();

            // Definindo as chaves estrangeiras
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
            $table->foreign('music_id')->references('id')->on('musics')->onDelete('cascade');

            // Evita duplicação do mesmo relacionamento
            $table->unique(['artist_id', 'music_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('artist_music');
    }
};
