<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenteTelemetria extends Model
{
    protected $table = 'agente_telemetria';

    protected $fillable = [
        'unidade_id','agent_key','agent_version','cpu_usage','ram_used',
        'internet_status','latency_ms','agent_uptime','system_uptime','last_error'
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }
}
