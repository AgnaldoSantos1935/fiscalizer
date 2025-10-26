<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoramentoLog extends Model
{
    protected $fillable = [
        'monitoramento_id', 'online', 'status_code',
        'latencia', 'erro', 'verificado_em'
    ];

    public function monitoramento()
    {
        return $this->belongsTo(Monitoramento::class);
    }

}
