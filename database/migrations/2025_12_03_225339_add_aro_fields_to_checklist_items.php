<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            // Campos para o ARO
            $table->string('probability')->nullable()->after('help_text');
            $table->string('severity')->nullable()->after('probability');
            $table->string('risk_level')->nullable()->after('severity');
            $table->text('mitigation')->nullable()->after('risk_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn(['probability', 'severity', 'risk_level', 'mitigation']);
        });
    }
};