<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class AtaRegistroPreco extends Model
{
    use SoftDeletes;

    protected $table = 'atas_registro_precos';

    protected $fillable = [
        'numero',
        'processo',
        'orgao_gerenciador_id',
        'fornecedor_id',
        'objeto',
        'data_publicacao',
        'vigencia_inicio',
        'vigencia_fim',
        'situacao',
        'prorroga_total_meses',
        'prorroga_json',
        'saldo_global',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data_publicacao' => 'date',
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'prorroga_json' => 'array',
        'saldo_global' => 'decimal:2',
    ];

    public function orgaoGerenciador()
    {
        return $this->belongsTo(Empresa::class, 'orgao_gerenciador_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Empresa::class, 'fornecedor_id');
    }

    public function itens()
    {
        return $this->hasMany(AtaItem::class, 'ata_id');
    }

    public function adesoes()
    {
        return $this->hasMany(AtaAdesao::class, 'ata_id');
    }

    public function getVigenciaFinalAttribute()
    {
        $fim = $this->vigencia_fim ? Carbon::parse($this->vigencia_fim) : null;
        $meses = (int) ($this->prorroga_total_meses ?? 0);

        return $fim ? $fim->copy()->addMonths($meses) : null;
    }

    public function getSituacaoVigenciaAttribute()
    {
        $final = $this->vigencia_final;
        if (! $final) {
            return null;
        }

        return now()->lte($final) ? 'vigente' : 'expirada';
    }

    public function updateSituacaoAutomatic(): void
    {
        $s = $this->situacao_vigencia;
        if ($s && $s !== $this->situacao) {
            $this->situacao = $s;
            $this->save();
        }
    }

    public function getSaldoConsumidoAttribute()
    {
        return (float) ($this->adesoes()->where('status', 'autorizada')->sum('valor_estimado') ?? 0);
    }

    public function getSaldoDisponivelAttribute()
    {
        $base = (float) ($this->saldo_global ?? 0);
        $consumido = (float) $this->saldo_consumido;
        $disp = $base - $consumido;

        return $disp > 0 ? $disp : 0.0;
    }
}
