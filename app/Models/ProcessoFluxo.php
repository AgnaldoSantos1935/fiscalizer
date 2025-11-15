<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoFluxo extends Model
{
    protected $fillable = [
        'processo_id', 'etapa_origem_id', 'etapa_destino_id',
        'regra', 'acao_automatica',
    ];

    protected $casts = [
        'regra' => 'array',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    public function etapaOrigem()
    {
        return $this->belongsTo(ProcessoEtapa::class, 'etapa_origem_id');
    }

    public function etapaDestino()
    {
        return $this->belongsTo(ProcessoEtapa::class, 'etapa_destino_id');
    }
}
