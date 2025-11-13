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
        Schema::create('settings_revenue_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Meta 1"
            $table->decimal('min_value', 12, 2); // Ex: 0.00
            $table->decimal('max_value', 12, 2); // Ex: 15000.00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_revenue_tiers');
    }
};