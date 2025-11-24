<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitacaoEmpenho extends Model
{
    use HasFactory;

    protected $table = 'solicitacoes_empenho';

    protected $fillable = [
        'contrato_id',
        'medicao_id',
        'usuario_solicitante_id',
        'numero_processo',
        'pdf_pretensao',
        'status',
        'observacoes',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }
}
