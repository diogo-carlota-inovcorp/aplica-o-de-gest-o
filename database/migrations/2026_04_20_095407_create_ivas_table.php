<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ivas', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: "IVA Normal", "IVA Reduzido"
            $table->decimal('percentagem', 5, 2); // 23.00, 13.00, 6.00
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ivas');
    }
};
