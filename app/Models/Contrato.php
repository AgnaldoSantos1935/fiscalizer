<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pessoa;

class Contrato extends Model
{
    use SoftDeletes;

    protected $table = 'contratos';

    protected $fillable = [
        'numero',
        'objeto',
        'contratada_id',
        'fiscal_tecnico_id',
        'fiscal_administrativo_id',
        'gestor_id',
        'valor_global',
        'data_inicio',
        'data_fim',
        'situacao',
        'tipo',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['data_inicio', 'data_fim', 'deleted_at'];

    protected $casts = [
        'valor_global' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * 🔹 Empresa contratada
     */
    public function contratada()
    {
        return $this->belongsTo(Empresa::class, 'contratada_id');
    }

    /**
     * 🔹 Fiscal técnico responsável
     */
    public function fiscalTecnico()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_tecnico_id');
    }

    /**
     * 🔹 Fiscal administrativo
     */
    public function fiscalAdministrativo()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_administrativo_id');
    }

    /**
     * 🔹 Gestor do contrato
     */
    public function gestor()
    {
        return $this->belongsTo(Pessoa::class, 'gestor_id');
    }


    /**
     * 🔹 Empenhos vinculados
     */
    public function empenhos()
    {
        return $this->hasMany(Empenho::class, 'contrato_id');
    }

    public function situacaoContrato()
    {
        return $this->belongsTo(SituacaoContrato::class, 'id');
    }

    /**
     * 🔹 Itens de contrato
     */
    public function itens()
    {
        return $this->hasMany(ContratoItem::class, 'contrato_id');
    }

    /**
     * 🔹 Usuário criador
     */
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 🔹 Usuário que atualizou
     */
    public function atualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * 🔎 Escopo: apenas contratos vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('situacao', 'vigente');
    }

    /**
     * 🔎 Escopo: contratos por tipo
     */
    public function getValorEmpenhadoAttribute()
    {
        return $this->empenhos->sum('valor');
    }

    public function getValorPagoAttribute()
    {
        return $this->empenhos->sum(fn($e) => $e->pagamentos->sum('valor_pagamento'));
    }

    public function getSaldoContratoAttribute()
    {
        return $this->valor_global - $this->valor_pago;
    }

    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    public function fiscais()
    {
        return $this->belongsToMany(User::class, 'contrato_fiscais')
            ->withPivot('tipo')
            ->withTimestamps();
    }

    public function fiscaisTecnicos()
    {
        return $this->fiscais()->wherePivot('tipo', 'fiscal_tecnico');
    }

    public function fiscaisAdministrativos()
    {
        return $this->fiscais()->wherePivot('tipo', 'fiscal_administrativo');
    }

    public function gestores()
    {
        return $this->fiscais()->wherePivot('tipo', 'gestor');
    }

}
