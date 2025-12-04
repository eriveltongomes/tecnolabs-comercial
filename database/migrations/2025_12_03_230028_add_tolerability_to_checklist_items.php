<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->string('tolerability')->nullable()->after('risk_level'); // Ex: "Aceitável", "Inaceitável"
        });
    }

    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn('tolerability');
        });
    }
};
