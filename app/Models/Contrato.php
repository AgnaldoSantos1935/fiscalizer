<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrato extends Model
{
    use SoftDeletes;

    protected $table = 'contratos';

    protected $fillable = [
        'numero',
        'objeto',
        'contratada_id',
        'fiscal_tecnico_id',
        'fiscal_administrativo_id',
        'gestor_id',
        'valor_global',
        'data_inicio',
        'data_fim',
        'situacao',
        'tipo',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['data_inicio', 'data_fim'];

    /**
     * 🔹 Empresa contratada (chave estrangeira: contratada_id)
     */
    public function contratada()
    {
        return $this->belongsTo(Empresa::class, 'contratada_id');
    }

    /**
     * 🔹 Fiscal técnico responsável
     */
    public function fiscalTecnico()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_tecnico_id');
    }

    /**
     * 🔹 Fiscal administrativo
     */
    public function fiscalAdministrativo()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_administrativo_id');
    }

    /**
     * 🔹 Gestor do contrato
     */
    public function gestor()
    {
        return $this->belongsTo(Pessoa::class, 'gestor_id');
    }

    /**
     * 🔹 Itens contratados (relacionamento 1:N)
     */
public function itens()
{
    return $this->hasMany(ContratoItem::class, 'contrato_id');
}

}
