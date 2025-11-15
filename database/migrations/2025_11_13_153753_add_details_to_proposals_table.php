<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona as colunas necessárias para o layout do PDF.
     */
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('service_location')->nullable()->after('total_value');
            $table->date('service_date')->nullable()->after('service_location');
            $table->text('payment_terms')->nullable()->after('service_date');
            $table->string('courtesy')->nullable()->after('payment_terms');
            $table->text('scope_description')->nullable()->after('courtesy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Remove as colunas se a migração for revertida
            $table->dropColumn([
                'service_location',
                'service_date',
                'payment_terms',
                'courtesy',
                'scope_description'
            ]);
        });
    }
};