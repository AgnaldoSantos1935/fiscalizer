<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NormaTrecho extends Model
{
    protected $table = 'normas_trechos';

    protected $fillable = [
        'fonte',
        'referencia',
        'idioma',
        'arquivo_pdf',
        'trecho_ordem',
        'trecho_texto',
        'tags',
        'embedding',
    ];

    protected $casts = [
        'tags' => 'array',
        'embedding' => 'array',
    ];
}

