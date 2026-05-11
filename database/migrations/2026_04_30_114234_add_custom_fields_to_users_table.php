<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adicionar colunas SEM a foreign key
            if (!Schema::hasColumn('users', 'telemovel')) {
                $table->string('telemovel')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'grupo_permissao_id')) {
                $table->unsignedBigInteger('grupo_permissao_id')->nullable()->after('telemovel');
            }
            if (!Schema::hasColumn('users', 'estado')) {
                $table->boolean('estado')->default(true)->after('password');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telemovel', 'grupo_permissao_id', 'estado']);
        });
    }
};
