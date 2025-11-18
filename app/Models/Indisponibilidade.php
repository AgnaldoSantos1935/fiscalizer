<?php

// app/Models/Indisponibilidade.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indisponibilidade extends Model
{
    protected $fillable = [
        'host_id', 'inicio', 'fim', 'duracao_segundos', 'motivo', 'detalhes',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(Host::class);
    }
}
