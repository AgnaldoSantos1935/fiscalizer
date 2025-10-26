<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OcorrenciaFiscalizacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ocorrencias_fiscalizacao';

    protected $fillable = [
        'contrato_id',
        'data_ocorrencia',
        'tipo',
        'descricao',
        'responsavel_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Uma ocorrência pertence a um contrato.
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Responsável (usuário ou fiscal) vinculado à ocorrência.
     */
    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    /**
     * Usuário que criou a ocorrência.
     */
    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuário que atualizou a ocorrência.
     */
    public function atualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
