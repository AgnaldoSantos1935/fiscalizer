<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Escola extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'escolas';

    protected $fillable = [
        'codigo',
        'restricao_atendimento',
        'nome',
        'codigo_inep',
        'uf',
        'municipio',
        'localizacao',
        'localidade_diferenciada',
        'categoria_administrativa',
        'endereco',
        'telefone',
        'dependencia_administrativa',
        'categoria_escola_privada',
        'conveniada_poder_publico',
        'regulamentacao_conselho',
        'porte',
        'etapas_modalidades',
        'outras_ofertas',
        'latitude',
        'longitude',
    ];

}
