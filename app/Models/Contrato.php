<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';          // nome da tabela
    protected $primaryKey = 'id_contrato';   // nome correto da PK
    public $incrementing = true;             // PK auto_increment
    protected $keyType = 'int';              // tipo da chave

    protected $fillable = [
        'numero',
        'objeto',
        'contratada_id',
        'valor_global',
        'data_inicio',
        'data_fim',
        'situacao',
        'gestor_id',
        'fiscal_id',
        'tipo'
    ];

    // ✅ Relacionamento com empresa (contratada)
    public function contratada()
    {
        return $this->belongsTo(Empresa::class, 'contratada_id', 'id_empresa');
    }

    // ✅ Relacionamento com medições
    public function medicoes()
    {
        return $this->hasMany(Medicao::class, 'contrato_id', 'id_contrato');
    }
}

