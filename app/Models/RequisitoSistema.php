<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitoSistema extends Model
{
    use HasFactory;

    protected $table = 'requisitos_sistema';

    protected $fillable = [
        'projeto_id', 'descricao', 'tipo', 'complexidade',
        'pontos_previstos', 'responsavel_id'
    ];

    protected $casts = [
        'pontos_previstos' => 'float',
    ];

    public function projeto() { return $this->belongsTo(Projeto::class); }
    public function responsavel() { return $this->belongsTo(Pessoa::class, 'responsavel_id'); }

    protected static function booted() {
        static::saving(function ($req) {
            $mapa = ['baixa' => 3, 'media' => 6, 'alta' => 9];
            $req->pontos_previstos = $req->pontos_previstos ?: $mapa[$req->complexidade] ?? 0;
        });
    }
}
