<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderChecklist extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = [
        'filled_at' => 'datetime',
    ];

    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
    public function checklistModel() { return $this->belongsTo(ChecklistModel::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function answers() { return $this->hasMany(ChecklistAnswer::class); }
}