<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings_equipment', function (Blueprint $table) {
            // Adiciona a coluna 'type' após o 'id', com valor padrão 'drone' para não quebrar os atuais
            $table->enum('type', ['drone', 'camera', 'acessorio', 'outros'])
                  ->default('drone')
                  ->after('id');
        });
    }

    public function down()
    {
        Schema::table('settings_equipment', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};