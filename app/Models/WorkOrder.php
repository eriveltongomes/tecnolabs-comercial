<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'client_id',
        'technician_id',
        'title',
        'description',
        'service_type',
        'service_location',
        'scheduled_at',
        'started_at',    // Campo novo
        'finished_at',   // Campo novo
        'status',
        'decea_protocol',
        'flight_max_altitude'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Relacionamentos
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // CORREÇÃO AQUI: O nome correto do Model é WorkOrderChecklist
    public function checklists()
    {
        return $this->hasMany(WorkOrderChecklist::class);
    }

    public function equipments()
    {
        return $this->belongsToMany(Settings\Equipment::class, 'work_order_equipment', 'work_order_id', 'equipment_id')
                    ->withTimestamps();
    }
}