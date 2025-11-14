<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apf extends Model
{
    protected $table = 'apfs';

    protected $fillable = [
        'projeto_id',
        'total_pf',
        'observacao',
        // demais campos da sua tabela APF…
    ];

    public function projeto()
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }
}
