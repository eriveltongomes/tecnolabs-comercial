<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Illuminate\Support\Facades\Hash; // Não precisamos mais deste import aqui

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
        'decea_profile_id',
        'profile_photo', // <--- ADICIONADO: Permite o upload pelo Admin
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
        // 'password' => 'hashed', // No Laravel 8/9 isso não é obrigatório se fizermos manual, mas no 10+ ajuda.
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

    // OSs onde este usuário é o Técnico Responsável
    public function workOrdersAsTechnician()
    {
        return $this->hasMany(WorkOrder::class, 'technician_id');
    }

    /* --- OBSERVAÇÃO IMPORTANTE ---
       Removi o método setPasswordAttribute abaixo.
       Motivo: No UserController, já estamos usando Hash::make().
       Se deixarmos aqui, o Laravel vai criptografar a senha duas vezes (hash do hash),
       e o usuário não vai conseguir logar.
    */
    
    // public function setPasswordAttribute($value)
    // {
    //    $this->attributes['password'] = Hash::make($value);
    // }
}