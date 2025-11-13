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
        // Schema::table('proposals', function (Blueprint $table) {
        //     $table->string('service_location')->nullable()->after('total_value');
        //     ... (tudo comentado)
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['service_location', 'service_date', 'payment_terms', 'courtesy', 'scope_description']);
        });
    }
};