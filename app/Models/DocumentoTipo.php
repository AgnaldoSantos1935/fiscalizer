<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoTipo extends Model
{
    protected $table = 'documento_tipos';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'permite_nova_data_fim',
        'ativo',
    ];

    protected $casts = [
        'permite_nova_data_fim' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'documento_tipo_id');
    }
}
