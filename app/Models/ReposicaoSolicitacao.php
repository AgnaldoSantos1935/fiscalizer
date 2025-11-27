<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReposicaoSolicitacao extends Model
{
    protected $table = 'reposicao_solicitacoes';

    protected $fillable = [
        'unidade_id','equipamento_id','contrato_item_id','quantidade','status','motivo',
        'cit_decisao','cit_observacoes','cit_usuario_id','detec_usuario_id',
        'aprovada_em','entregue_em','baixado_em',
    ];

    protected $casts = [
        'aprovada_em' => 'datetime',
        'entregue_em' => 'datetime',
        'baixado_em' => 'datetime',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function contratoItem()
    {
        return $this->belongsTo(ContratoItem::class);
    }
}

