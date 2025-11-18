<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicaoItemTelco extends Model
{
    protected $table = 'medicao_itens_telco';

    protected $fillable = [
        'medicao_id', 'escola_id', 'localidade', 'link_id', 'uptime_percent',
        'downtime_minutos', 'qtd_quedas', 'valor_mensal_contratado',
        'valor_desconto', 'valor_final', 'eventos_json',
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

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }
}
