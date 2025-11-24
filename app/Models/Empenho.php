<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empenho extends Model
{
    use SoftDeletes;

    protected $table = 'empenhos';

    protected $fillable = [
        'numero',
        'contrato_id',
        'empresa_id',
        'solicitacao_empenho_id',
        'medicao_id',
        'processo',
        'programa_trabalho',
        'fonte_recurso',
        'natureza_despesa',
        'data_lancamento',
        'solicitado_at',
        'valor_extenso',
        'valor_total',
        'emitido_pdf_path',
        'emitido_at',
        'pago_comprovante_path',
        'pago_at',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'solicitado_at' => 'datetime',
        'valor_total' => 'decimal:2',
        'emitido_at' => 'datetime',
        'pago_at' => 'datetime',
    ];

    // 🔗 Relacionamentos
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function itens()
    {
        return $this->hasMany(EmpenhoItem::class, 'nota_empenho_id');
    }

    public function solicitacao()
    {
        return $this->belongsTo(SolicitacaoEmpenho::class, 'solicitacao_empenho_id');
    }

    public function medicao()
    {
        return $this->belongsTo(Medicao::class, 'medicao_id');
    }

    // 🔄 Cálculo automático do valor total
    protected static function booted()
    {
        static::saved(function ($empenho) {
            $total = $empenho->itens()->sum('valor_total');
            if ($empenho->valor_total != $total) {
                $empenho->updateQuietly(['valor_total' => $total]);
            }
        });
    }

    // 💰 Formatação amigável
    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total ?? 0, 2, ',', '.');
    }

    public function getDataFormatadaAttribute()
    {
        return optional($this->data_lancamento)?->format('d/m/Y') ?? '—';
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamentos::class);
    }

    public function solicitacoes()
    {
        return $this->hasMany(EmpenhoSolicitacao::class, 'empenho_id');
    }
}
