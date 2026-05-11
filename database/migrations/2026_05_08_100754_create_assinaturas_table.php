<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locatario_id')->constrained('locatarios')->onDelete('cascade');
            $table->foreignId('plano_id')->constrained('planos');
            $table->enum('ciclo_cobranca', ['mensal', 'anual'])->default('mensal');
            $table->enum('estado', ['ativa', 'cancelada', 'expirada', 'pendente'])->default('ativa');
            $table->timestamp('data_inicio');
            $table->timestamp('data_fim')->nullable();
            $table->timestamp('data_cancelamento')->nullable();
            $table->json('metadados')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assinaturas');
    }
};
