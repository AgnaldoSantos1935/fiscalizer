<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicaoItemFixoMensal extends Model
{
    protected $fillable = [
        'medicao_id','descricao','servico_prestado','relatorio_entregue',
        'chamados_atendidos','chamados_pendentes',
        'valor_mensal_contratado','valor_desconto','valor_final','observacoes_json'
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }
}
