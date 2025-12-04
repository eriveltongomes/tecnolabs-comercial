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
        Schema::create('work_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('checklist_model_id')->constrained('checklist_models');
            
            $table->foreignId('user_id')->nullable()->constrained('users'); 
            $table->timestamp('filled_at')->nullable();
            
            $table->enum('risk_level', ['baixo', 'medio', 'alto'])->nullable();
            $table->text('comments')->nullable();
            $table->string('location_coordinates')->nullable();
            $table->timestamps();
        });

        Schema::create('checklist_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_checklist_id')->constrained('work_order_checklists')->onDelete('cascade');
            $table->foreignId('checklist_item_id')->constrained('checklist_items');
            $table->boolean('is_ok')->default(true);
            $table->text('observation')->nullable();
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
        Schema::dropIfExists('work_order_checklists');
    }
};
