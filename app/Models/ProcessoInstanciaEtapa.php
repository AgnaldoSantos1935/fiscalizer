<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoInstanciaEtapa extends Model
{
    protected $fillable = [
        'instancia_id', 'etapa_id', 'status',
        'data_inicio', 'data_fim', 'responsavel_id', 'observacoes',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    public function instancia()
    {
        return $this->belongsTo(ProcessoInstancia::class, 'instancia_id');
    }

    public function etapa()
    {
        return $this->belongsTo(ProcessoEtapa::class, 'etapa_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}
