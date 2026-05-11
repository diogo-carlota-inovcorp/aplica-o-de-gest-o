<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calendario_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cor')->default('#3b82f6');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendario_tipos');
    }
};
