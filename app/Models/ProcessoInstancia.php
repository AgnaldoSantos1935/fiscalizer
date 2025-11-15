<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoInstancia extends Model
{
    protected $fillable = [
        'processo_id', 'referencia_type', 'referencia_id',
        'status', 'iniciado_por', 'data_inicio', 'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    public function referencia()
    {
        return $this->morphTo();
    }

    public function etapas()
    {
        return $this->hasMany(ProcessoInstanciaEtapa::class, 'instancia_id');
    }

    public function logs()
    {
        return $this->hasMany(ProcessoLog::class, 'instancia_id');
    }

    public function etapaAtual()
    {
        return $this->etapas()
            ->whereIn('status', ['pendente', 'em_execucao', 'atrasada'])
            ->orderBy('id')
            ->first();
    }
}
