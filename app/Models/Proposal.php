<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    /**
     * Permite atribuição em massa.
     */
    protected $guarded = [];

    /**
     * Casts para ENUMs, JSON e DATAS
     */
    protected $casts = [
        'service_details' => 'array',
        'status' => 'string',
        'service_type' => 'string',
        'approved_at' => 'datetime',
        'service_date' => 'date', // <--- ESTA LINHA RESOLVE O ERRO
    ];

    /**
     * O Vendedor (User) que criou a proposta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * O Cliente desta proposta.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * O Canal (Channel) desta proposta.
     */
    public function channel()
    {
        return $this->belongsTo(Settings\Channel::class);
    }

    /**
     * O Financeiro (User) que aprovou a proposta.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Os custos variáveis desta proposta.
     */
    public function variableCosts()
    {
        return $this->hasMany(ProposalVariableCost::class);
    }
}