<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipeProjeto extends Model
{
    use HasFactory;

    protected $table = 'equipes_projeto';

    protected $fillable = [
        'projeto_id', 'pessoa_id', 'perfil', 'horas_previstas', 'horas_realizadas',
    ];

    protected $casts = [
        'horas_previstas' => 'float',
        'horas_realizadas' => 'float',
    ];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
