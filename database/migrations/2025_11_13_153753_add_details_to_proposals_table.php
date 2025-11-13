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
        Schema::table('proposals', function (Blueprint $table) {
            // Adiciona os novos campos necessários para o PDF
            $table->string('service_location')->nullable()->after('total_value');
            $table->date('service_date')->nullable()->after('service_location');
            $table->text('payment_terms')->nullable()->after('service_date');
            $table->string('courtesy')->nullable()->after('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['service_location', 'service_date', 'payment_terms', 'courtesy']);
        });
    }
};