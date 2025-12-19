<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueTier extends Model
{
    use HasFactory;

    // A LINHA MÁGICA: Aponta para a tabela correta no seu banco
    protected $table = 'settings_revenue_tiers';

    protected $fillable = [
        'name',
        'min_value',
        'max_value',
    ];
}