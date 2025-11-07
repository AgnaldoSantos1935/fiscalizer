<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Escola extends Model
{
    use HasFactory;

    protected $table = 'escolas';

    protected $fillable = [
        'id',
        'restricao_atendimento',
        'escola',
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
        'regulamentacao_conselho_educacao',
        'porte_escola',
        'etapas_modalidades_oferecidas',
        'outras_ofertas_educacionais',
        'latitude',
        'longitude',
        'dre',
    ];

    /**
     * Campos que devem ser tratados como números decimais.
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Desabilitar SoftDeletes se não houver campo deleted_at.
     */
    public $timestamps = true;

    /**
     * Exemplo de relacionamento (caso exista tabela DREs).
     */
    public function dreRelacionada()
    {
        return $this->belongsTo(DRE::class, 'dre', 'codigodre');
    }
public function hosts()
{
    return $this->hasMany(Host::class, 'local', 'id_escola');
}

    /**
     * Helper para formatar nome completo (exemplo).
     */
    public function getNomeFormatadoAttribute()
    {
        return mb_strtoupper($this->escola);
    }
}
