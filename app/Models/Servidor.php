<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{
    use HasFactory;

    protected $fillable = [
        'pessoa_id', 'matricula', 'cargo', 'funcao', 'lotacao',
        'data_admissao', 'vinculo', 'situacao', 'salario',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'salario' => 'decimal:2',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
