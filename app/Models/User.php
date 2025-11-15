<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Documento;
use App\Models\Medicao;
use App\Models\LogSistema;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',

        'name' => 'string',
        'email' => 'string',
        'role_id' => 'integer',
        ];
    }

public function role()
{
    return $this->belongsTo(Role::class);
}

/**
 * Verifica se o usuário possui o papel indicado
 * @param string|array $nomeRole Nome do papel ou array de nomes de papéis
 * @return bool
 */
public function hasRole($nomeRole)
{
    if (!$this->role) {
        return false;
    }

    if (is_array($nomeRole)) {
        return in_array($this->role->nome, $nomeRole);
    }

    return $this->role->nome === $nomeRole;
}
public function pessoa()
{
    return $this->hasOne(\App\Models\Pessoa::class);
}
public function documentos()
{
    return $this->hasMany(Documento::class, 'user_id');
}

public function medicoes()
{
    return $this->hasMany(Medicao::class, 'user_id');
}

public function logs()
{
    return $this->hasMany(LogSistema::class, 'user_id');
}

}
