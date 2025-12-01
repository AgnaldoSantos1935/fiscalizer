<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = 'unidades';

    protected $fillable = [
        'nome', 'tipo', 'telefone', 'inventario_token',
    ];

    public function equipamentos()
    {
        return $this->hasMany(Equipamento::class);
    }
}
