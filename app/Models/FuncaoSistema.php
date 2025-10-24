<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Medicao;

class FuncaoSistema extends Model
{
    protected $table = 'funcao_sistemas'; // 👈 importante

    protected $fillable = [
        'medicao_id', 'nome_funcao', 'tipo', 'complexidade', 'pontos', 'justificativa'
    ];


    protected static function booted()
    {
        static::creating(function ($funcao) {
            $funcao->pontos = self::calcularPontos($funcao->tipo, $funcao->complexidade);
        });

        static::saved(function ($funcao) {
            $funcao->medicao->atualizarTotais();
        });

        static::deleted(function ($funcao) {
            $funcao->medicao->atualizarTotais();
        });
    }

    public static function calcularPontos($tipo, $complexidade)
    {
        $matriz = [
            'EE'  => ['baixa' => 3, 'media' => 4, 'alta' => 6],
            'SE'  => ['baixa' => 4, 'media' => 5, 'alta' => 7],
            'CE'  => ['baixa' => 3, 'media' => 4, 'alta' => 6],
            'ALI' => ['baixa' => 7, 'media' => 10, 'alta' => 15],
            'AIE' => ['baixa' => 5, 'media' => 7, 'alta' => 10],
        ];
        return $matriz[$tipo][$complexidade] ?? 0;
    }

    public function medicao()
    {
       return $this->belongsTo(Medicao::class, 'medicao_id');
    }
}
