<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;
    protected $table="user_profiles";
    protected $fillable = [
        'user_id',
        'nome_completo',
        'cpf',
        'rg',
        'data_nascimento',
        'idade',
        'sexo',
        'signo',
        'mae',
        'pai',
        'tipo_sanguineo',
        'altura',
        'peso',
        'cor_preferida',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'telefone_fixo',
        'celular',
        'email_pessoal',
        'email_institucional',
        'matricula',
        'cargo',
        'dre',
        'lotacao',
        'foto',
        'observacoes',
        'data_atualizacao',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}









































