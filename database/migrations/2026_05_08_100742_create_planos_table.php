<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->decimal('preco_mensal', 10, 2)->default(0);
            $table->decimal('preco_anual', 10, 2)->default(0);
            $table->json('funcionalidades')->nullable();
            $table->json('limites')->nullable(); // utilizadores, armazenamento, etc.
            $table->boolean('tem_periodo_teste')->default(true);
            $table->integer('dias_teste')->default(14);
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planos');
    }
};
