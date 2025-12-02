<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoMedicaoPagamento extends Model
{
    protected $table = 'contrato_medicao_pagamentos';

    protected $fillable = [
        'medicao_id',
        'numero_documento',
        'valor_pago',
        'data_pagamento',
    ];

    public function medicao()
    {
        return $this->belongsTo(ContratoMedicao::class, 'medicao_id');
    }
}
