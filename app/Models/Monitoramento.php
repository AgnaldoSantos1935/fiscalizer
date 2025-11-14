<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoramento extends Model
{
    protected $table = 'monitoramentos';

    protected $fillable = [
        'host_id',
        'online',
        'status_code',
        'latencia',
        'jitter',
        'perda_pacotes',
        'tempo_resposta',
        'cpu',
        'memoria_usada',
        'memoria_total',
        'rx_rate',
        'tx_rate',
        'download',
        'upload',
        'erro',
        'dados_extra',
        'duracao_online',
        'duracao_offline',
        'ultima_verificacao',
    ];

    protected $casts = [
        'dados_extra'        => 'array',
        'ultima_verificacao' => 'datetime',
        'online'             => 'boolean',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class);
    }
}
