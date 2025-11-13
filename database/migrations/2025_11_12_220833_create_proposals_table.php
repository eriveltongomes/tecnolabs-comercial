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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_number')->unique();
            
            // Chaves Estrangeiras
            $table->foreignId('user_id')->constrained('users'); // Vendedor
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('channel_id')->constrained('settings_channels');
            
            // Status e Serviço
            $table->enum('status', ['rascunho', 'aberta', 'em_analise', 'aprovada', 'reprovada', 'cancelada'])->default('aberta');
            $table->enum('service_type', ['drone', 'timelapse', 'tour_virtual']);
            
            // Detalhes e Valores
            $table->json('service_details')->nullable(); // Guarda dados como {"period": 8, "labor_cost": 500}
            $table->decimal('total_value', 12, 2);
            $table->decimal('commission_value', 10, 2)->nullable(); // Preenchido na aprovação
            
            // Aprovação (Financeiro)
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};