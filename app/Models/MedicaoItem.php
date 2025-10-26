<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicaoItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medicao_id', 'projeto_id', 'descricao',
        'pontos_funcao', 'ust',
        'valor_unitario_pf', 'valor_unitario_ust', 'valor_total',
        'created_by', 'updated_by',
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    public function projeto()
    {
        return $this->belongsTo(Projeto::class);
    }

    public $timestamps = true;
}
