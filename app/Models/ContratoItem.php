<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoItem extends Model
{
    protected $table = 'contrato_itens';

    protected $fillable = [
        'contrato_id',
        'descricao_item',
        'unidade_medida',
        'quantidade',
        'meses',
        'valor_unitario',
        'valor_total',
        'tipo_item',
        'status',
        'created_by'
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->valor_total = $item->quantidade * $item->valor_unitario;
        });
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function medicoesItens()
    {
        return $this->hasMany(MedicaoItem::class, 'item_id');
    }

    public function empenhos()
    {
        return $this->hasMany(Empenho::class, 'item_id');
    }
}
