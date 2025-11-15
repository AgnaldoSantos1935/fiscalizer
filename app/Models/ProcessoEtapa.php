<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoEtapa extends Model
{
    protected $fillable = [
        'processo_id', 'nome', 'ordem', 'tipo', 'prazo_horas',
        'responsavel_tipo', 'checklist', 'ativa',
    ];

    protected $casts = [
        'checklist' => 'array',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    public function fluxosOrigem()
    {
        return $this->hasMany(ProcessoFluxo::class, 'etapa_origem_id');
    }

    public function fluxosDestino()
    {
        return $this->hasMany(ProcessoFluxo::class, 'etapa_destino_id');
    }
}
