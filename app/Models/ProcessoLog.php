<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoLog extends Model
{
    protected $fillable = [
        'instancia_id', 'etapa_id', 'acao', 'usuario_id', 'mensagem',
    ];

    public function instancia()
    {
        return $this->belongsTo(ProcessoInstancia::class, 'instancia_id');
    }

    public function etapa()
    {
        return $this->belongsTo(ProcessoEtapa::class, 'etapa_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
