<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'filtros',
        'user_id',
    ];

    protected $casts = [
        'filtros' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
