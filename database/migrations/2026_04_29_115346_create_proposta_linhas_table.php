<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposta_linhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained('propostas')->onDelete('cascade');
            $table->foreignId('artigo_id')->constrained('artigos');
            $table->foreignId('fornecedor_id')->nullable()->constrained('entidades');
            $table->decimal('preco_custo', 12, 2);
            $table->integer('quantidade')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposta_linhas');
    }
};
