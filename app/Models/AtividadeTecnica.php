<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtividadeTecnica extends Model
{
    protected $table = 'atividades_tecnicas';

    protected $fillable = [
        'projeto_id',
        'etapa',
        'analista',
        'data',
        'horas',
        'descricao',
    ];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }
}
