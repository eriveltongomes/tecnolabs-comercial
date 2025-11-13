<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueTier extends Model
{
    use HasFactory;
    protected $table = 'settings_revenue_tiers'; // Especifica o nome da tabela
    protected $guarded = [];
}