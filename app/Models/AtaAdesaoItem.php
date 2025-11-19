<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtaAdesaoItem extends Model
{
    protected $table = 'ata_adesao_itens';

    protected $fillable = [
        'adesao_id',
        'ata_item_id',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:4',
        'valor_total' => 'decimal:2',
    ];

    public function adesao()
    {
        return $this->belongsTo(AtaAdesao::class, 'adesao_id');
    }

    public function item()
    {
        return $this->belongsTo(AtaItem::class, 'ata_item_id');
    }
}
