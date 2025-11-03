<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Situacao extends Model
{
    protected $table = 'situacoes';

    protected $fillable = [
        'nome',
        'descricao',
        'cor',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * 🔹 Relação com contratos (1:N)
     */
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'situacao_id');
    }

    /**
     * 🔹 Escopo: somente situações ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * 🔹 Escopo: busca por nome
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where('nome', 'like', "%{$termo}%");
    }
}
