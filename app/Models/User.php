<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash; // --- IMPORTAMOS O HASH ---

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // --- A LINHA 'password' => 'hashed' FOI REMOVIDA DAQUI ---
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

    // --- MUTATOR (MÉTODO CORRETO PARA LARAVEL 9) ---

    /**
     * Sempre faz o Hash da senha ao defini-la.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}