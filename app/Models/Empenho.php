<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pagamentos;

class Empenho extends Model
{
    protected $fillable = [
        'empresa_id',
        'contrato_id',
        'numero',
        'data_lancamento',
        'processo',
        'programa_trabalho',
        'fonte_recurso',
        'natureza_despesa',
        'contrato_numero',
        'credor_nome',
        'cnpj',
        'valor_total',
        'valor_extenso',
        'ordenador_nome',
        'ordenador_cpf',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /** 🔹 Relacionamentos */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itens()
    {
        return $this->hasMany(EmpenhoItem::class);
    }

    /** 🔹 Preenchimento automático */
    public static function booted()
    {
        static::creating(function ($empenho) {
            if ($empenho->contrato_id) {
                $contrato = Contrato::with('contratada')->find($empenho->contrato_id);
                if ($contrato) {
                    $empenho->contrato_numero = $contrato->numero;
                    $empenho->empresa_id = $contrato->contratada_id;
                    $empenho->credor_nome = $contrato->contratada->razao_social;
                    $empenho->cnpj = $contrato->contratada->cnpj ?? '';
                    $empenho->valor_total = $contrato->valor_global;
                }
            }
        });
    }
    public function pagamentos()
{
    return $this->hasMany(Pagamentos::class);
}

public function getValorPagoAttribute()
{
    return $this->pagamentos->sum('valor_pagamento');
}

public function getSaldoEmpenhoAttribute()
{
    return $this->valor - $this->valor_pago;
}

}
