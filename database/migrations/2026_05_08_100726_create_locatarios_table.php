<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locatarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('logotipo')->nullable();
            $table->json('configuracoes')->nullable(); // Cores, branding, etc.
            $table->enum('estado', ['ativo', 'suspenso', 'pendente', 'expirado'])->default('pendente');
            $table->timestamp('data_fim_teste')->nullable();
            $table->timestamp('data_fim_subscricao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locatarios');
    }
};
