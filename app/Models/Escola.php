<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    protected $table = 'escolas';

    protected $fillable = [
        'codigo',
        'restricao_atendimento',
        'Escola',
        'inep',
        'uf',
        'Municipio',
        'Localizacao',
        'localidade_diferenciada',
        'categoria_administrativa',
        'endereco',
        'Telefone',
        'dependencia_administrativa',
        'categoria_escola_privada',
        'conveniada_poder_publico',
        'regulamentacao_conselho',
        'porte_escola',
        'etapas_modalidades_oferecidas',
        'outras_ofertas_educacionais',
        'latitude',
        'longitude',
    ];
}
