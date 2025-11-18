<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalizacaoProjeto extends Model
{
    protected $table = 'fiscalizacoes_projetos';

    protected $fillable = [
        'projeto_id', 'apf_id', 'tipo_fiscalizacao', 'data_verificacao', 'fiscal_responsavel',
        'descricao_verificacao', 'status', 'nivel_risco', 'evidencias', 'recomendacoes',
    ];

    protected $casts = ['data_verificacao' => 'date', 'evidencias' => 'array'];

    public function projeto()
    {
        return $this->belongsTo(ProjetoSoftware::class, 'projeto_id');
    }

    public function apf()
    {
        return $this->belongsTo(Apf::class, 'apf_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoProjeto::class, 'fiscalizacao_id');
    }
}
