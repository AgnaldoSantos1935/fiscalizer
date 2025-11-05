<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemEmpenho extends Model
{
     use SoftDeletes;

    protected $table = 'notas_empenho_itens';

    protected $fillable = [
        'nota_empenho_id', 'item_numero', 'descricao',
        'unidade', 'quantidade', 'valor_unitario', 'valor_total'
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function notaEmpenho()
    {
        return $this->belongsTo(NotaEmpenho::class, 'nota_empenho_id');
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }
}
