<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Verificar e adicionar telemovel
            if (!Schema::hasColumn('users', 'telemovel')) {
                $table->string('telemovel')->nullable()->after('email');
            }

            // Verificar e adicionar grupo_permissao_id
            if (!Schema::hasColumn('users', 'grupo_permissao_id')) {
                $table->unsignedBigInteger('grupo_permissao_id')->nullable()->after('telemovel');
                $table->foreign('grupo_permissao_id')
                      ->references('id')
                      ->on('grupos_permissao')
                      ->onDelete('set null');
            }

            // Verificar e adicionar estado
            if (!Schema::hasColumn('users', 'estado')) {
                $table->boolean('estado')->default(true)->after('password');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover foreign key primeiro
            if (Schema::hasColumn('users', 'grupo_permissao_id')) {
                $table->dropForeign(['grupo_permissao_id']);
                $table->dropColumn('grupo_permissao_id');
            }

            if (Schema::hasColumn('users', 'telemovel')) {
                $table->dropColumn('telemovel');
            }

            if (Schema::hasColumn('users', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
