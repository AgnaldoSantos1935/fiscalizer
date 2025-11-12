<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronogramaProjeto extends Model
{
    use HasFactory;

    protected $table = 'cronogramas_projeto';

    protected $fillable = [
        'projeto_id', 'etapa', 'data_inicio', 'data_fim',
        'responsavel_id', 'status', 'observacao'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function projeto() { return $this->belongsTo(Projeto::class); }
    public function responsavel() { return $this->belongsTo(Pessoa::class, 'responsavel_id'); }
}
