<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermoReferenciaItem extends Model
{
    protected $table = 'termos_referencia_itens';

    protected $fillable = [
        'termo_referencia_id',
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
            $qtd = (float) ($item->quantidade ?? 0);
            $vu  = (float) ($item->valor_unitario ?? 0);
            $item->valor_total = $qtd * $vu;
        });
    }

    public function termoReferencia()
    {
        return $this->belongsTo(TermoReferencia::class, 'termo_referencia_id');
    }
}