<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings_equipment', function (Blueprint $table) {
            // Adiciona a coluna de validade do seguro (pode ser nula)
            $table->date('insurance_expiry')->nullable()->after('insurance_company');
        });
    }

    public function down()
    {
        Schema::table('settings_equipment', function (Blueprint $table) {
            $table->dropColumn('insurance_expiry');
        });
    }
};