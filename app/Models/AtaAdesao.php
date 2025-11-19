<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtaAdesao extends Model
{
    protected $table = 'ata_adesoes';

    protected $fillable = [
        'ata_id',
        'orgao_adquirente_id',
        'justificativa',
        'status',
        'documento_pdf_path',
        'valor_estimado',
        'data_solicitacao',
        'data_decisao',
        'created_by',
    ];

    protected $casts = [
        'valor_estimado' => 'decimal:2',
        'data_solicitacao' => 'date',
        'data_decisao' => 'date',
    ];

    public function ata()
    {
        return $this->belongsTo(AtaRegistroPreco::class, 'ata_id');
    }

    public function orgaoAdquirente()
    {
        return $this->belongsTo(Empresa::class, 'orgao_adquirente_id');
    }

    public function processoInstancia()
    {
        return $this->morphOne(\App\Models\ProcessoInstancia::class, 'referencia');
    }

    public function itens()
    {
        return $this->hasMany(\App\Models\AtaAdesaoItem::class, 'adesao_id');
    }
}
