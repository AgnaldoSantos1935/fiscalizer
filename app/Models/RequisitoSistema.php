<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoSistema extends Model
{
    protected $table = 'requisitos';

    protected $fillable = [
        'demanda_id',
        'codigo_interno',
        'titulo',
        'descricao',
        'etapa',
        'tipo',
        'complexidade',
    ];

    public function demanda()
    {
        return $this->belongsTo(Demanda::class);
    }

    public function medicaoItens()
    {
        return $this->hasMany(MedicaoItem::class);
    }
}
