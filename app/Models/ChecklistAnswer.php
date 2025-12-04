<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistAnswer extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relacionamento com o formulário preenchido (Pai)
    public function workOrderChecklist() {
        return $this->belongsTo(WorkOrderChecklist::class, 'work_order_checklist_id');
    }

    // Relacionamento com a pergunta original (Item)
    public function checklistItem() {
        return $this->belongsTo(ChecklistItem::class);
    }
}