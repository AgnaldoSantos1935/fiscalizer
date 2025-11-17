<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contrato_id',
        'tipo',
        'documento_tipo_id',
        'titulo',
        'descricao',
        'caminho_arquivo',
        'versao',
        'data_upload',
        'nova_data_fim',
        'metadados',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'data_upload' => 'date',
        'nova_data_fim' => 'date',
        'metadados' => 'array',
    ];

    /**
     * Relação com contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Tipo documental (entidade)
     */
    public function documentoTipo()
    {
        return $this->belongsTo(DocumentoTipo::class, 'documento_tipo_id');
    }

    /**
     * Auditoria
     */
    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function atualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
