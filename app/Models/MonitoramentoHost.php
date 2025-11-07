<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoramentoHost extends Model
{
    protected $table = 'monitoramentos_hosts';

    protected $fillable = [
        'host_id',
        'ip',
        'status',
        'tempo_resposta',
        'saida_ping',
        'verificado_em',
    ];


public function historico($id)
{
    $logs = MonitoramentoHost::where('host_id', $id)
        ->orderByDesc('verificado_em')
        ->take(50)
        ->get();

    return response()->json($logs);
}
    public function host()
    {
        return $this->belongsTo(Host::class);
    }
}
