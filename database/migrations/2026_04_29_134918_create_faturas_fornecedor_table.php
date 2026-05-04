<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('faturas_fornecedor', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('data_fatura');
            $table->date('data_vencimento');
            $table->foreignId('fornecedor_id')->constrained('entidades')->onDelete('restrict');
            $table->foreignId('encomenda_fornecedor_id')->nullable()->constrained('encomenda_fornecedor')->onDelete('set null');
            $table->decimal('valor_total', 12, 2);
            $table->string('documento')->nullable();
            $table->string('comprovativo_pagamento')->nullable();
            $table->enum('estado', ['pendente', 'paga'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faturas_fornecedor');
    }
};
