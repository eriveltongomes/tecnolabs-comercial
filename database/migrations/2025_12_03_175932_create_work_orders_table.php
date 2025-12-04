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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('technician_id')->nullable()->constrained('users'); 
            $table->string('title'); 
            $table->text('description')->nullable(); 
            $table->enum('service_type', ['drone', 'timelapse', 'tour_virtual', 'manutencao', 'teste', 'outros'])->default('drone');
            $table->dateTime('scheduled_at')->nullable(); 
            $table->dateTime('started_at')->nullable();   
            $table->dateTime('finished_at')->nullable();  
            $table->string('service_location')->nullable();
            $table->string('decea_protocol')->nullable();   
            $table->integer('flight_max_altitude')->nullable();
            $table->enum('status', ['pendente', 'agendada', 'em_execucao', 'concluida', 'cancelada'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
};
