<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicaoNotaFiscal extends Model
{
    protected $table = 'medicao_notas_fiscais';

    protected $fillable = [
        'medicao_id', 'chave', 'numero', 'cnpj_prestador',
        'cnpj_tomador', 'valor', 'tipo', 'status', 'mensagem', 'retorno_api',
    ];

    protected $casts = [
        'retorno_api' => 'array',
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }
}
