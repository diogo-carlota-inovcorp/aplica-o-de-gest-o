<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calendario_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('entidade_id')->nullable()->constrained('entidades')->onDelete('set null');
            $table->foreignId('tipo_id')->nullable()->constrained('calendario_tipos')->onDelete('set null');
            $table->foreignId('acao_id')->nullable()->constrained('calendario_acoes')->onDelete('set null');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->integer('duracao')->comment('Duração em minutos');
            $table->string('partilha')->nullable();
            $table->string('conhecimento')->nullable();
            $table->enum('estado', ['pendente', 'concluida', 'cancelada'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendario_atividades');
    }
};
