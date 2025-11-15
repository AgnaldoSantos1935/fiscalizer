<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    protected $table = "demandas";
    protected $fillable = [
        'projeto_id',
        'sistema_id',
        'modulo_id',
        'tipo_manutencao',
        'titulo',
        'descricao',
        'data_abertura',
        'data_fechamento',
        'prioridade',
        'status',
    ];

    protected $dates = ['data_abertura', 'data_fechamento'];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }

    public function requisitos()
    {
        return $this->hasMany(Requisito::class);
    }

    public function medicaoItens()
    {
        return $this->hasMany(MedicaoItem::class);
    }
}
