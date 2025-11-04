<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $table = 'hosts';

    protected $fillable = [
        'nome',
        'endereco',
        'tipo',
        'porta',
        'localizacao',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /** 🔹 Rótulo amigável do tipo */
    public function getTipoFormatadoAttribute(): string
    {
        return strtoupper($this->tipo) === 'IP' ? 'Endereço IP' : 'Domínio / Link';
    }

    /** 🔹 Status textual */
    public function getAtivoTextoAttribute(): string
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    /** 🔹 Relacionamento com monitoramentos (opcional) */
    public function monitoramentos()
    {
        return $this->hasMany(\App\Models\Monitoramento::class, 'host_id');
    }
}
