<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostTeste extends Model
{
    protected $table = 'host_testes';

    protected $fillable = [
        'host_id', 'ip_origem', 'ip_destino', 'status_conexao',
        'latencia_ms', 'perda_pacotes', 'ttl', 'protocolo',
        'porta', 'tempo_resposta', 'traceroute', 'resolved_hostname',
        'resultado_json', 'modo_execucao', 'executado_por'
    ];

    protected $casts = [
        'resultado_json' => 'array',
        'latencia_ms' => 'float',
        'perda_pacotes' => 'float',
        'tempo_resposta' => 'float',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }
}

