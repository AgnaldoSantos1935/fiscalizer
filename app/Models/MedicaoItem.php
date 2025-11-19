<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicaoItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medicao_itens';

    protected $fillable = [
        'medicao_id', 'projeto_id', 'descricao',
        'pontos_funcao', 'ust',
        'valor_unitario_pf', 'valor_unitario_ust', 'valor_total',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'valor_unitario_pf' => 'decimal:2',
        'valor_unitario_ust' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    // app/Models/BoletimMedicao.php
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itemContrato()
    {
        return $this->belongsTo(ContratoItem::class, 'item_id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->item_unico_hash = sha1(
                ($item->demanda_id ?? '') . '-' .
                ($item->requisito_id ?? '') . '-' .
                ($item->quantidade_pf ?? 0) . '-' .
                ($item->tipo_contagem ?? '') . '-' .
                ($item->sistema_id ?? '') . '-' .
                ($item->modulo_id ?? '')
            );
        });
    }

    public $timestamps = true;
}
