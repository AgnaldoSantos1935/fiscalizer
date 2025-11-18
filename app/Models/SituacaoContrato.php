<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SituacaoContrato extends Model
{
    protected $table = 'situacoes_contratos';

    protected $fillable = ['nome', 'slug', 'descricao', 'cor'];

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'situacao_contrato_id');
    }
}
