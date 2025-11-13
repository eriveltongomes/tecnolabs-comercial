<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // Importante
use Spatie\Activitylog\LogOptions; // Importante

class Proposal extends Model
{
    use HasFactory, LogsActivity; // Ativa o Log

    protected $guarded = [];

    protected $casts = [
        'service_details' => 'array',
        'status' => 'string',
        'service_type' => 'string',
        'approved_at' => 'datetime',
        'service_date' => 'date',
    ];

    // CONFIGURAÇÃO DO LOG
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total_value', 'commission_value', 'rejection_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Proposta {$eventName}");
    }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function client() { return $this->belongsTo(Client::class); }
    public function channel() { return $this->belongsTo(Settings\Channel::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by_user_id'); }
    public function variableCosts() { return $this->hasMany(ProposalVariableCost::class); }
}