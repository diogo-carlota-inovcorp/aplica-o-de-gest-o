<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telemovel')->nullable();
            $table->string('email')->nullable();
            $table->boolean('consentimento_rgpd')->default(false);
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->foreignId('entidade_id')->nullable()->constrained('entidades')->onDelete('cascade');
            $table->foreignId('funcao_id')->nullable()->constrained('funcoes');
            $table->foreignId('locatario_id')->nullable()->constrained('locatarios')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contactos');
    }
};
