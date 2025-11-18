<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpenhoItem extends Model
{
    use SoftDeletes;

    protected $table = 'notas_empenho_itens';

    protected $fillable = [
        'nota_empenho_id',
        'item_numero',
        'descricao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'valor_total',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }

    public function notaEmpenho()
    {
        return $this->belongsTo(Empenho::class, 'nota_empenho_id');
    }
}
