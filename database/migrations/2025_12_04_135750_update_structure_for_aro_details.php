<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Dados legais do Equipamento
        Schema::table('settings_equipment', function (Blueprint $table) {
            $table->string('anac_registration')->nullable()->after('name'); // Ex: PP-123456
            $table->string('insurance_policy')->nullable()->after('anac_registration'); // Nº Apólice
            $table->string('insurance_company')->nullable()->after('insurance_policy'); // Seguradora (Mapfre, etc)
        });

        // 2. Dados legais do Piloto (User)
        Schema::table('users', function (Blueprint $table) {
            $table->string('decea_profile_id')->nullable()->after('email'); // ID DECEA/SARPAS
        });

        // 3. Tabela de Ligação (OS <-> Equipamentos)
        // Permite selecionar vários equipamentos para uma OS
        Schema::create('equipment_work_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('settings_equipment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_work_order');
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('decea_profile_id'); });
        Schema::table('settings_equipment', function (Blueprint $table) {
            $table->dropColumn(['anac_registration', 'insurance_policy', 'insurance_company']);
        });
    }
};