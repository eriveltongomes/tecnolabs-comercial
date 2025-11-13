<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    use HasFactory;
    protected $table = 'settings_commission_rules'; // Especifica o nome da tabela
    protected $guarded = [];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function revenueTier()
    {
        return $this->belongsTo(RevenueTier::class);
    }
}