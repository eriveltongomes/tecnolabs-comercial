<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalVariableCost extends Model
{
    use HasFactory;

    /**
     * Permite atribuição em massa.
     */
    protected $guarded = [];

    /**
     * A Proposta (Proposal) a que este custo pertence.
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}