<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'matricula',
        'cpf',
        'email',
        'telefone',
        'cargo',
        'tipo_vinculo',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Relacionamentos de auditoria
    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function atualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
