<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrato extends Model
{
    use HasFactory, SoftDeletes;

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

    public $timestamps = true;

    /**
     * Relações
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'contratada_id');
    }

    public function fiscalTecnico()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_tecnico_id');
    }

    public function fiscalAdministrativo()
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_administrativo_id');
    }

    public function gestor()
    {
        return $this->belongsTo(Pessoa::class, 'gestor_id');
    }

    /**
     * Auditoria
     */
    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function atualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
