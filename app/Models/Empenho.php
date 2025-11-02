<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empenho extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contrato_id',
        'numero',
        'data_empenho',
        'valor',
        'descricao',
        'projeto_atividade',
        'fonte_recurso',
        'status',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
