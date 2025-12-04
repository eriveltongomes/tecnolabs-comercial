<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- IMPORTANTE

class ChecklistItem extends Model
{
    use HasFactory, SoftDeletes; // <--- IMPORTANTE
    
    protected $guarded = [];

    public function model() {
        return $this->belongsTo(ChecklistModel::class);
    }
}