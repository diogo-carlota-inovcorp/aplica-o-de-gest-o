<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locatario_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locatario_id')->constrained('locatarios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('funcao', ['proprietario', 'admin', 'membro'])->default('membro');
            $table->json('permissoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('convidado_em')->nullable();
            $table->timestamp('aceitou_em')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locatario_user');
    }
};
