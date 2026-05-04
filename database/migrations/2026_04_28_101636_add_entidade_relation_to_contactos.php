<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            // Se ainda não tiver a foreign key
            if (!Schema::hasColumn('contactos', 'entidade_id')) {
                $table->foreignId('entidade_id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contactos', function (Blueprint $table) {
            $table->dropForeign(['entidade_id']);
            $table->dropColumn('entidade_id');
        });
    }
};
