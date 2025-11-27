<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipamentoReposicaoHistorico extends Model
{
    protected $table = 'equipamento_reposicao_historicos';

    protected $fillable = [
        'unidade_id','equipamento_id','reposicao_id','novo_equipamento_id','evento','usuario_id','observacoes',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function novoEquipamento()
    {
        return $this->belongsTo(Equipamento::class, 'novo_equipamento_id');
    }

    public function reposicao()
    {
        return $this->belongsTo(ReposicaoSolicitacao::class, 'reposicao_id');
    }
}

