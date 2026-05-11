<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('encomenda_fornecedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encomenda_id')->constrained('encomendas')->onDelete('cascade');
            $table->foreignId('fornecedor_id')->constrained('entidades');
            $table->string('numero_fornecedor')->unique();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['pendente', 'aprovada', 'entregue'])->default('pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('encomenda_fornecedor');
    }
};
