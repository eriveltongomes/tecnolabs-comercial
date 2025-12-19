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
    public function up()
{
    Schema::create('monthly_goals', function (Blueprint $table) {
        $table->id();
        $table->integer('month'); // Ex: 12
        $table->integer('year');  // Ex: 2025
        $table->decimal('amount', 15, 2); // Ex: 100000.00
        $table->unsignedBigInteger('user_id')->nullable(); // Quem definiu a meta (opcional)
        $table->timestamps();

        // Garante que não teremos duas metas para o mesmo mês/ano
        $table->unique(['month', 'year']);
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_goals');
    }
};
