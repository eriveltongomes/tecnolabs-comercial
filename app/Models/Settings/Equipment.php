<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WorkOrder;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'settings_equipment';

    protected $fillable = [
        'type',
        'name',
        'invested_value', 
        'lifespan_hours',
        'anac_registration',
        'insurance_policy',
        'insurance_company',
        'insurance_expiry'
    ];

    public function workOrders()
    {
        return $this->belongsToMany(WorkOrder::class, 'work_order_equipment');
    }
}