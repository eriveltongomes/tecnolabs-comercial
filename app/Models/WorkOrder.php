<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Equipment; // Importante

class WorkOrder extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function proposal() { return $this->belongsTo(Proposal::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function technician() { return $this->belongsTo(User::class, 'technician_id'); }
    
    // Relacionamento com os Checklists vinculados (Um para Muitos)
    public function checklists() {
        return $this->hasMany(WorkOrderChecklist::class);
    }

    // Relacionamento com Equipamentos (Muitos para Muitos) - NOVO
    public function equipments() {
        return $this->belongsToMany(Equipment::class, 'equipment_work_order');
    }
}