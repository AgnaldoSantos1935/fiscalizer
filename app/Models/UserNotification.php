<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id', 'tipo', 'titulo', 'mensagem', 'link', 'lida', 'lida_em'
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
