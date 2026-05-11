<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('entidades', function (Blueprint $table) {
            if (!Schema::hasColumn('entidades', 'locatario_id')) {
                $table->foreignId('locatario_id')->nullable()->constrained('locatarios')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('entidades', function (Blueprint $table) {
            $table->dropForeign(['locatario_id']);
            $table->dropColumn('locatario_id');
        });
    }
};
