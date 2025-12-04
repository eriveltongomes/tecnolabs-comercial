<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistModel extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function items() {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }
}