<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoProjeto extends Model
{
    protected $table = 'documentos_projetos';

    protected $fillable = ['fiscalizacao_id', 'tipo', 'arquivo', 'titulo', 'observacao'];

    public function fiscalizacao()
    {
        return $this->belongsTo(FiscalizacaoProjeto::class, 'fiscalizacao_id');
    }
}
