<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoramento extends Model
{
    protected $fillable = [
        'nome', 'tipo', 'alvo', 'porta', 'ativo', 'online',
        'status_code', 'latencia', 'erro', 'ultima_verificacao'
    ];
    public function logs()
{
    return $this->hasMany(MonitoramentoLog::class);
}

}
