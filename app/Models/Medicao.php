<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicao extends Model
{
    protected $table = "medicoes";
    protected $fillable = [
        'contrato_id','competencia','tipo','valor_bruto','valor_desconto',
        'valor_liquido','sla_alcancado','sla_contratado','status',
        'resumo_json','inconsistencias_json'
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itensSoftware()
    {
        return $this->hasMany(MedicaoItemSoftware::class);
    }

    public function itensTelco()
    {
        return $this->hasMany(MedicaoItemTelco::class);
    }

    public function itensFixo()
    {
        return $this->hasMany(MedicaoItemFixoMensal::class);
    }
}
