<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicaoItemFixoMensal extends Model
{
    protected $fillable = [
        'medicao_id', 'descricao', 'servico_prestado', 'relatorio_entregue',
        'chamados_atendidos', 'chamados_pendentes',
        'valor_mensal_contratado', 'valor_desconto', 'valor_final', 'observacoes_json',
    ];

    protected $casts = [
        'valor_mensal_contratado' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_final' => 'decimal:2',
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }
}
