<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipamentoOcorrencia extends Model
{
    protected $table = 'equipamento_ocorrencias';

    protected $fillable = [
        'equipamento_id','tipo','descricao','fotos','status',
        'reportado_by','recebida_por','avaliada_por','analise_status','analise_observacoes',
    ];

    protected $casts = [
        'fotos' => 'array',
    ];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}

