<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs_alteracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locatario_id')->constrained('locatarios')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('tipo'); // plano, utilizador, configuracao
            $table->string('acao');
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs_alteracoes');
    }
};
