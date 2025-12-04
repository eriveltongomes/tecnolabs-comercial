<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'decea_profile_id', // Campo novo da fase anterior
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // --- RELACIONAMENTOS ---

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'user_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'created_by_user_id');
    }

    public function approvedProposals()
    {
        return $this->hasMany(Proposal::class, 'approved_by_user_id');
    }

    // NOVO: OSs onde este usuário é o Técnico Responsável
    public function workOrdersAsTechnician()
    {
        return $this->hasMany(WorkOrder::class, 'technician_id');
    }

    // --- MUTATOR ---
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}