<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TesteConexao extends Model
{
    protected $table = 'testes_rede';

    protected $fillable = [
        'alvo',
        'tipo',
        'dns',
        'ping',
        'http_status',
        'http_ok',
        'http_erro',
        'user_id',
    ];

    protected $casts = [
        'http_ok' => 'boolean',
        'data_teste' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
