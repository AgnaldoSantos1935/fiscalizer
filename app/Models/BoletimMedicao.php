<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletimMedicao extends Model
{
    use HasFactory;

    protected $table = 'boletins_medicao';

    protected $fillable = [
        'medicao_id', 'projeto_id', 'total_pf', 'total_ust',
        'valor_total', 'data_emissao', 'observacao'
    ];

    protected $casts = [
        'total_pf' => 'float',
        'total_ust' => 'float',
        'valor_total' => 'float',
        'data_emissao' => 'date',
    ];

    public function medicao() { return $this->belongsTo(Medicao::class); }
    public function projeto() { return $this->belongsTo(Projeto::class); }

    protected static function booted() {
        static::creating(function ($b) {
            // Calcula valor total automaticamente (PF * UST)
            if ($b->valor_total == 0 && $b->total_pf > 0) {
                $b->valor_total = ($b->total_pf * 100) + ($b->total_ust * 200); // Exemplo
            }
        });
    }
}
