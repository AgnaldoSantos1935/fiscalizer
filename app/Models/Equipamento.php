<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipamento extends Model
{
    protected $fillable = [
        'serial_number','hostname','sistema_operacional','ram_gb',
        'cpu_resumida','ip_atual','discos','ultimo_checkin',
        'origem_inventario','unidade_id','tipo','especificacoes'
    ];

    protected $casts = [
        'discos' => 'array',
        'ultimo_checkin' => 'datetime',
        'especificacoes' => 'array',
    ];

    public function unidade()
    {
        return $this->belongsTo(Unidade::class);
    }
}
