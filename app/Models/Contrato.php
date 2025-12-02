<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pagamentos;
use Illuminate\Support\Facades\DB;

class Contrato extends Model
{


    protected $table = 'contratos';

    protected $fillable = [
        'numero',
        'ano',
        'processo_administrativo',
        'fundamentacao_legal',
        'objeto',
        'contratada_id',
        'fiscal_tecnico_id',
        'suplente_fiscal_tecnico_id',
        'suplente_fiscal_tecnico_ativo',
        'fiscal_administrativo_id',
        'suplente_fiscal_administrativo_id',
        'suplente_fiscal_administrativo_ativo',
        'gestor_id',
        'valor_global',
        'data_inicio',
        'data_fim',
        'situacao',
        'tipo',
        'created_by',
        'updated_by',

        'contratante_json',
        'contratada_representante_json',
        'vigencia_info_json',
        'dotacao_orcamentaria_json',
        'reajuste_json',
        'garantia_json',
        'pagamento_json',
        'fiscalizacao_json',
        'penalidades_json',
        'rescisao_json',
        'lgpd_json',
        'publicacao_doe_json',
    ];

    protected $dates = ['data_inicio', 'data_fim', 'deleted_at'];

    protected $casts = [
        'valor_global' => 'decimal:2',
        'valor_mensal' => 'decimal:2',
        'ano' => 'integer',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'suplente_fiscal_tecnico_ativo' => 'boolean',
        'suplente_fiscal_administrativo_ativo' => 'boolean',

        'contratante_json' => 'array',
        'contratada_representante_json' => 'array',
        'vigencia_info_json' => 'array',
        'dotacao_orcamentaria_json' => 'array',
        'reajuste_json' => 'array',
        'garantia_json' => 'array',
        'pagamento_json' => 'array',
        'fiscalizacao_json' => 'array',
        'penalidades_json' => 'array',
        'rescisao_json' => 'array',
        'lgpd_json' => 'array',
        'publicacao_doe_json' => 'array',
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

    public function suplenteFiscalTecnico()
    {
        return $this->belongsTo(Pessoa::class, 'suplente_fiscal_tecnico_id');
    }

    public function suplenteFiscalAdministrativo()
    {
        return $this->belongsTo(Pessoa::class, 'suplente_fiscal_administrativo_id');
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
        return $this->belongsTo(SituacaoContrato::class, 'situacao_contrato_id');
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
        return $this->empenhos->sum('valor_total');
    }

    public function getValorPagoAttribute()
    {
        return $this->empenhos->sum(fn ($e) => $e->pagamentos->sum('valor_pagamento'));
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

    public function usuarioVinculado(User $user): bool
    {
        $pessoaId = Pessoa::where('user_id', $user->id)->value('id');

        if ($pessoaId) {
            if (in_array($pessoaId, [
                $this->fiscal_tecnico_id,
                $this->fiscal_administrativo_id,
                $this->gestor_id,
            ], true)) {
                return true;
            }
            if ($this->suplente_fiscal_tecnico_ativo && $pessoaId === $this->suplente_fiscal_tecnico_id) {
                return true;
            }
            if ($this->suplente_fiscal_administrativo_ativo && $pessoaId === $this->suplente_fiscal_administrativo_id) {
                return true;
            }
        }

        return $this->fiscais()->where('users.id', $user->id)->exists();
    }

    public function scopeDoUsuario($query, User $user)
    {
        $pessoaId = Pessoa::where('user_id', $user->id)->value('id');

        return $query->where(function ($q) use ($user, $pessoaId) {
            if ($pessoaId) {
                $q->where('fiscal_tecnico_id', $pessoaId)
                    ->orWhere('fiscal_administrativo_id', $pessoaId)
                    ->orWhere('gestor_id', $pessoaId)
                    ->orWhere(function ($qq) use ($pessoaId) {
                        $qq->where('suplente_fiscal_tecnico_id', $pessoaId)
                            ->where('suplente_fiscal_tecnico_ativo', true);
                    })
                    ->orWhere(function ($qq) use ($pessoaId) {
                        $qq->where('suplente_fiscal_administrativo_id', $pessoaId)
                            ->where('suplente_fiscal_administrativo_ativo', true);
                    });
            }

            $q->orWhereExists(function ($sub) use ($user) {
                $sub->select(DB::raw(1))
                    ->from('contrato_fiscais')
                    ->whereColumn('contrato_fiscais.contrato_id', 'contratos.id')
                    ->where('contrato_fiscais.user_id', $user->id);
            });
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * 🔹 Documentos vinculados ao contrato
     */
    public function documentos()
    {
        return $this->hasMany(\App\Models\Documento::class, 'contrato_id');
    }

    public function getDataFinalAttribute()
    {
        $fimContrato = $this->getAttribute('data_fim') ?? $this->getAttribute('data_fim_vigencia');
        // Considera apenas documentos cujo tipo permite alterar a vigência
        $novaDatas = $this->documentos
            ? $this->documentos
                ->filter(function ($doc) {
                    return optional($doc->documentoTipo)->permite_nova_data_fim && ! empty($doc->nova_data_fim);
                })
                ->pluck('nova_data_fim')
                ->filter()
                ->map(fn ($d) => Carbon::parse($d))
            : collect();

        $dataFimContrato = $fimContrato ? Carbon::parse($fimContrato) : null;
        $dataFinal = $dataFimContrato;

        if ($novaDatas->isNotEmpty()) {
            $maisRecente = $novaDatas->max();
            if (! $dataFinal || $maisRecente->gt($dataFinal)) {
                $dataFinal = $maisRecente;
            }
        }

        return $dataFinal;
    }

    public function getVigenciaMesesAttribute()
    {
        $inicio = $this->getAttribute('data_inicio')
            ?? $this->getAttribute('data_inicio_vigencia')
            ?? $this->getAttribute('data_assinatura');

        $final = $this->data_final;

        if (! $inicio || ! $final) {
            return null;
        }

        $inicioC = Carbon::parse($inicio);
        $finalC = $final instanceof Carbon ? $final : Carbon::parse($final);

        $meses = $inicioC->diffInMonths($finalC);

        return $meses;
    }
    /**
 * Medições mensais do contrato de serviço
 */
public function medicoes()
{
    return $this->hasMany(Medicao::class, 'contrato_id');
}
/**
 * Pagamentos do contrato via empenhos intermediários
 */
public function pagamentos()
{
    return $this->hasManyThrough(
        Pagamentos::class,
        Empenho::class,
        'contrato_id',
        'empenho_id',
        'id',
        'id'
    );
}


}
