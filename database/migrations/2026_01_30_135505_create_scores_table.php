<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            // PERHATIKAN BARIS INI: Harus 'game_id', bukan 'game_version_id'
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('game_id')->constrained()->onDelete('cascade'); 
            $table->double('score');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scores');
    }
};