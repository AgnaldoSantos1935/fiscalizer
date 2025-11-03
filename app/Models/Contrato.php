<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'situacao_id',
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
     * 🔹 Situação (referência à tabela 'situacoes')
     */
    public function situacao()
    {
        return $this->belongsTo(Situacao::class, 'situacao_id');
    }

    /**
     * 🔹 Empenhos vinculados
     */
    public function empenhos()
    {
        return $this->hasMany(Empenho::class, 'contrato_id');
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
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
