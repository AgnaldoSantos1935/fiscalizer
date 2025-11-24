<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpenhoSolicitacao extends Model
{
    use SoftDeletes;

    protected $table = 'empenho_solicitacoes';

    protected $fillable = [
        'empenho_id',
        'contrato_id',
        'empresa_id',
        'mes',
        'ano',
        'periodo_referencia',
        'observacoes',
        'dados',
        'status',
        'solicitado_by',
        'solicitado_at',
        'aprovado_by',
        'aprovado_at',
        'pdf_path',
    ];

    protected $casts = [
        'dados' => 'array',
        'solicitado_at' => 'datetime',
        'aprovado_at' => 'datetime',
    ];

    public function empenho()
    {
        return $this->belongsTo(Empenho::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitado_by');
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_by');
    }
}
