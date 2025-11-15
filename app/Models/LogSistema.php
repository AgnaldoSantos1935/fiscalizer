<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    use HasFactory;

    protected $table = 'log_sistema';

    protected $fillable = [
        'usuario_id',
        'acao',
        'detalhes',
        'ip',
        'user_agent',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
