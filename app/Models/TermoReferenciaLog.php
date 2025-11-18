<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermoReferenciaLog extends Model
{
    protected $table = 'termo_referencia_logs';

    protected $fillable = [
        'termo_referencia_id',
        'acao',
        'usuario_id',
        'motivo',
    ];

    public function termoReferencia()
    {
        return $this->belongsTo(TermoReferencia::class, 'termo_referencia_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}