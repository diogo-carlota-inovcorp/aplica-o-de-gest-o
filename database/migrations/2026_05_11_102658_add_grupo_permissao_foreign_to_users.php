<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Verificar se a tabela grupos_permissao já existe
        if (Schema::hasTable('grupos_permissao')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'grupo_permissao_id')) {
                    // Verificar se a foreign key já não existe
                    $table->foreign('grupo_permissao_id')
                          ->references('id')
                          ->on('grupos_permissao')
                          ->onDelete('set null');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['grupo_permissao_id']);
        });
    }
};
