<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoletimMedicao extends Model
{
    protected $table = 'boletins_medicao';

    protected $fillable = [
        'medicao_id',
        'projeto_id',
        'total_pf',
        'total_ust',
        'valor_total',
        'data_emissao',
        'observacao',
    ];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }
}
