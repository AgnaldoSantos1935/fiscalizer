<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicao extends Model
{
    protected $table = 'medicao'; // 👈 Força o nome correto da tabela

    protected $fillable = [
        'contrato_id',
        'mes_referencia',
        'total_pf',
        'valor_unitario_pf',
        'valor_total',
        'data_envio',
        'status',
        'observacao',
    ];

    public function contrato()
    {
          return $this->belongsTo(Contrato::class, 'contrato_id', 'id_contrato');
    }

    public function funcoes()
    {
        return $this->hasMany(FuncaoSistema::class, 'medicao_id');
    }
}
