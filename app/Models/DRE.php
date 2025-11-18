<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dre extends Model
{
    use HasFactory;

    protected $table = 'dres';

    protected $fillable = [
        'id',
        'codigodre',
        'nome_dre',
        'municipio_sede',
        'email',
        'telefone',
        'endereco',
    ];

    public function escolas()
    {
        return $this->hasMany(Escola::class, 'dre');
    }
}
