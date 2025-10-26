<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medicoes';
    protected $fillable = [
        'contrato_id',
        'mes_referencia',
        'total_pf',
        'valor_unitario_pf',
        'valor_total',
        'data_envio',
        'status',
        'observacao',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    /**
     * Relação com o contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
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

    /**
     * Retorna o valor total calculado dinamicamente (opcional)
     */
    public function getValorCalculadoAttribute()
    {
        return $this->total_pf * $this->valor_unitario_pf;
    }
}
