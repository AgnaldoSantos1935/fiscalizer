<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonitoramentoLog extends Model
{
    protected $table = 'monitoramento_logs';

    protected $fillable = [
        'monitoramento_id',
        'online',
        'status_code',
        'latencia',
        'erro',
        'data_teste',
    ];

    protected $casts = [
        'online' => 'boolean',
        'latencia' => 'float',
        'data_teste' => 'datetime',
    ];

    /**
     * 🔹 Relacionamento com o monitoramento principal
     */
    public function monitoramento()
    {
        return $this->belongsTo(Monitoramento::class, 'monitoramento_id');
    }

    /**
     * 🔹 Texto legível do status
     */
    public function getStatusTextAttribute()
    {
        return $this->online ? '🟢 Online' : '🔴 Offline';
    }

    /**
     * 🔹 Data formatada
     */
    public function getDataFormatadaAttribute()
    {
        return Carbon::parse($this->data_teste)->format('d/m/Y H:i:s');
    }
}
