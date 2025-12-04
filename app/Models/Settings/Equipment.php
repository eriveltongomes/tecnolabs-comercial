<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkOrder; // Importante

class Equipment extends Model
{
    use HasFactory;
    protected $table = 'settings_equipment'; 
    protected $guarded = []; 

    // Relacionamento Inverso: Equipamento -> Ordens de Serviço
    public function workOrders() {
        return $this->belongsToMany(WorkOrder::class, 'equipment_work_order');
    }
}