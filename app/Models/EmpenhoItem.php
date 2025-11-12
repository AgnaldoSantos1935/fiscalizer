<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpenhoItem extends Model
{
    use SoftDeletes;

    protected $table = 'empenho_itens';

    protected $fillable = [
        'empenho_id',
        'item_numero',
        'descricao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'valor_total'
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }

    public function notaEmpenho()
    {
        return $this->belongsTo(NotaEmpenho::class, 'nota_empenho_id');
    }
}
