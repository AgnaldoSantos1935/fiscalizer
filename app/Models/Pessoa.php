<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pessoa extends Model
{
    use HasFactory;

    protected $table = "pessoas";
    protected $fillable = [
        'user_id','nome_completo','cpf','rg','data_nascimento','sexo',
        'email','telefone','cep','logradouro','numero','bairro','cidade','uf'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function servidor() {
        return $this->hasOne(Servidor::class);
    }
}
