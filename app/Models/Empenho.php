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
        'processo',
        'programa_trabalho',
        'fonte_recurso',
        'natureza_despesa',
        'data_lancamento',
        'valor_extenso',
        'valor_total'
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'valor_total' => 'decimal:2',
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
        return $this->hasMany(NotaEmpenhoItem::class, 'nota_empenho_id');
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
}
