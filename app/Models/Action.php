<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $table = 'actions';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'modulo',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_actions');
    }
}
