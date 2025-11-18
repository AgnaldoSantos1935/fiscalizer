<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamentos extends Model
{
    protected $fillable = ['empenho_id', 'valor_pagamento', 'data_pagamento', 'documento', 'observacao'];

    protected $casts = [
        'valor_pagamento' => 'decimal:2',
    ];

    public function empenho()
    {
        return $this->belongsTo(Empenho::class);
    }
}
