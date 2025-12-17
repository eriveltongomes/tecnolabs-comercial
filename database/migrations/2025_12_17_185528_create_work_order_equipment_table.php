<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cria a tabela pivô
        Schema::create('work_order_equipment', function (Blueprint $table) {
            $table->id();
            
            // Chave estrangeira para a Ordem de Serviço
            $table->foreignId('work_order_id')
                  ->constrained('work_orders')
                  ->onDelete('cascade');

            // Chave estrangeira para o Equipamento (Apontando para settings_equipment)
            $table->foreignId('equipment_id')
                  ->constrained('settings_equipment')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_order_equipment');
    }
};