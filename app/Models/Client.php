<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * Permite atribuição em massa.
     */
    protected $guarded = [];

    /**
     * O Vendedor (User) que cadastrou este cliente.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Todas as propostas deste cliente.
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}