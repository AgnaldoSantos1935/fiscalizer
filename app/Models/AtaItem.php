<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtaItem extends Model
{
    protected $table = 'ata_itens';

    protected $fillable = [
        'ata_id',
        'descricao',
        'unidade',
        'quantidade',
        'preco_unitario',
        'lote',
        'grupo',
        'marca',
        'referencia',
        'saldo_disponivel',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'preco_unitario' => 'decimal:4',
        'saldo_disponivel' => 'decimal:2',
    ];

    public function ata()
    {
        return $this->belongsTo(AtaRegistroPreco::class, 'ata_id');
    }
}
