<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'uf',
        'cep',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

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

    /**
     * Relacionamento com contratos
     */
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'contratada_id');
    }
}
