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
        Schema::create('settings_commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('settings_channels');
            $table->foreignId('revenue_tier_id')->constrained('settings_revenue_tiers');
            $table->decimal('percentage', 5, 2); // Ex: 6.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_commission_rules');
    }
};