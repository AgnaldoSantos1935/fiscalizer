<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MonitoramentoHost;

class MedicaoItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "medicao_itens";
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
// app/Models/BoletimMedicao.php
public function host()
{
    return $this->belongsTo(Host::class);
}

public function contrato()
{
    return $this->belongsTo(Contrato::class);
}

public function itemContrato()
{
    return $this->belongsTo(ItemContrato::class, 'item_id');
}

public function escola()
{
    return $this->belongsTo(Escola::class);
}



    public $timestamps = true;
}
