<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('empresa_configs', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('nome');
            $table->string('morada')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('localidade')->nullable();
            $table->string('nif', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresa_configs');
    }
};
