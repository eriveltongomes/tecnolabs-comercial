<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\CommissionRule; // <-- Importar o modelo de Regra

class Channel extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'settings_channels';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array
     */
    protected $fillable = ['name'];


    // --- A SOLUÇÃO ESTÁ AQUI ---
    // Define a relação que faltava: Um Canal tem muitas Regras de Comissão.
    public function commissionRules()
    {
        return $this->hasMany(CommissionRule::class);
    }
}