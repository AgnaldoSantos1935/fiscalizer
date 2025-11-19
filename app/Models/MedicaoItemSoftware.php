<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicaoItemSoftware extends Model
{
    protected $table = "medicao_itens_software'";

    protected $fillable = [
        'medicao_id', 'demanda_id', 'os_id', 'sistema', 'modulo', 'descricao',
        'pf', 'ust', 'horas', 'qtd_pessoas', 'valor_unitario_pf', 'valor_unitario_ust',
        'valor_total', 'hash_unico',
    ];

    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->hash_unico = sha1(
                $item->demanda_id . '|' . $item->os_id . '|' . $item->descricao . '|' . $item->pf . '|' . $item->ust
            );
        });
    }
}
