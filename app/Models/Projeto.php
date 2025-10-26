<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projeto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contrato_id',
        'nome',
        'descricao',
        'status',
        'data_inicio',
        'data_fim',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    /**
     * Relacionamento com Contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relacionamento com Itens de Medição
     */
    public function itensMedicao()
    {
        return $this->hasMany(MedicaoItem::class);
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

