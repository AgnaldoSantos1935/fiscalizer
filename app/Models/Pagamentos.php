<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamentos extends Model
{
    protected $fillable = ['empenho_id', 'valor_pagamento', 'data_pagamento', 'documento', 'observacao'];

    public function empenho()
    {
        return $this->belongsTo(Empenho::class);
    }
}

