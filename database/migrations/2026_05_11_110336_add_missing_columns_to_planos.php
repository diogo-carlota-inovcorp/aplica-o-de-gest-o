<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('planos', function (Blueprint $table) {
            // Verificar e adicionar colunas apenas se não existirem
            if (!Schema::hasColumn('planos', 'limites')) {
                $table->json('limites')->nullable()->after('preco_anual');
            }

            if (!Schema::hasColumn('planos', 'funcionalidades')) {
                $table->json('funcionalidades')->nullable()->after('limites');
            }

            if (!Schema::hasColumn('planos', 'dias_teste')) {
                $table->integer('dias_teste')->default(14)->after('funcionalidades');
            }

            if (!Schema::hasColumn('planos', 'tem_periodo_teste')) {
                $table->boolean('tem_periodo_teste')->default(true)->after('dias_teste');
            }
        });
    }

    public function down()
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->dropColumn(['limites', 'funcionalidades', 'dias_teste', 'tem_periodo_teste']);
        });
    }
};
