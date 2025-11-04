<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Monitoramento extends Model
{
    protected $table = 'monitoramentos';

    protected $fillable = [
        'nome',
        'tipo',
        'alvo',
        'porta',
        'ativo',
        'online',
        'status_code',
        'latencia',
        'erro',
        'ultima_verificacao',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'online' => 'boolean',
        'latencia' => 'float',
        'ultima_verificacao' => 'datetime',
    ];

    // 🔹 Helper: status formatado
    public function getStatusTextAttribute()
    {
        return $this->online ? '🟢 Online' : '🔴 Offline';
    }

    // 🔹 Helper: última verificação legível
    public function getUltimaVerificacaoFormatadaAttribute()
    {
        return $this->ultima_verificacao
            ? Carbon::parse($this->ultima_verificacao)->diffForHumans()
            : 'Nunca verificado';
    }
}
