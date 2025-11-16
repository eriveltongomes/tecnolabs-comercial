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
            
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('channel_id')->constrained('settings_channels');
            
            // --- A CORREÇÃO ESTÁ AQUI ---
            // Adicionamos 'recusada' na lista
            $table->enum('status', ['rascunho', 'aberta', 'em_analise', 'aprovada', 'reprovada', 'cancelada', 'recusada'])->default('rascunho');
            
            $table->enum('service_type', ['drone', 'timelapse', 'tour_virtual']);
            
            $table->json('service_details')->nullable(); 
            $table->decimal('total_value', 12, 2);
            $table->decimal('commission_value', 10, 2)->nullable(); 
            
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