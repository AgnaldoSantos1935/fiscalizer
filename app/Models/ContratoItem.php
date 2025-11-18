<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContratoItem extends Model
{
    use SoftDeletes;

    protected $table = 'contrato_itens';

    protected $fillable = [
        'contrato_id',
        'descricao_item',
        'unidade_medida',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'tipo_item',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    // 🔗 Relação com o contrato
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    // ⚙️ Cálculo automático do valor total
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }
}
