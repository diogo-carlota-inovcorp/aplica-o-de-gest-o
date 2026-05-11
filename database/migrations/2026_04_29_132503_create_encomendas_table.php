<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->date('data_encomenda');
            $table->foreignId('cliente_id')->constrained('entidades')->onDelete('restrict');
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');
            $table->foreignId('proposta_id')->nullable()->constrained('propostas')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('encomendas');
    }
};
