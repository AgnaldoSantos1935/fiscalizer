<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemFornecimento extends Model
{
    protected $table = 'ordens_fornecimento';

    protected $fillable = [
        'contrato_id',
        'empenho_id',
        'numero_of',
        'ano_of',
        'data_emissao',
        'arquivo_pdf',
        'itens_json',
        'orgao_entidade',
        'unidade_requisitante',
        'cnpj_orgao',
        'contrato_numero',
        'processo_contratacao',
        'modalidade',
        'vigencia_inicio',
        'vigencia_fim',
        'fundamentacao_legal',
        'contratada_razao_social',
        'contratada_cnpj',
        'contratada_endereco',
        'contratada_representante',
        'contratada_contato',
        'prazo_entrega_dias',
        'local_entrega',
        'horario_entrega',
        'recebimento_condicoes',
        'obrigacoes_contratada',
        'obrigacoes_administracao',
        'sancoes',
        'autoridade_nome',
        'autoridade_cargo',
        'gestor_nome',
        'gestor_portaria',
        'fiscal_nome',
        'fiscal_portaria',
        'assinaturas_json',
        'assinatura_hash',
        'verificacao_url',
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'itens_json' => 'array',
        'assinaturas_json' => 'array',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function empenho()
    {
        return $this->belongsTo(Empenho::class, 'empenho_id');
    }

    public function getValorTotalAttribute(): float
    {
        $items = collect($this->itens_json ?? []);

        return (float) $items->sum(fn ($i) => (float) ($i['valor_total'] ?? 0));
    }
}
