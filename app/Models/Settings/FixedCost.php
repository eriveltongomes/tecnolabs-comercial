<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedCost extends Model
{
    use HasFactory;
    protected $table = 'settings_fixed_costs'; // Especifica o nome da tabela
    protected $guarded = [];
}