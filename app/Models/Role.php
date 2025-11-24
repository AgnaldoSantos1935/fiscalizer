<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    /**
     * Cada papel pode ter vários usuários.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'role_actions');
    }

    /**
     * Verifica se o papel possui a ação informada.
     * Suporta wildcard no formato "modulo.*" e códigos com ponto (ex.: contratos.edit).
     */
    public function hasAction(string $codigo): bool
    {
        $actions = $this->actions()->pluck('codigo')->all();
        if (in_array($codigo, $actions)) {
            return true;
        }

        // wildcard: modulo.*
        // Extrai o módulo pela primeira ocorrência de separador (ponto ou underline)
        $parts = preg_split('/[._]/', $codigo);
        $modulo = $parts[0] ?? $codigo;

        return in_array($modulo . '.*', $actions);
    }
}
