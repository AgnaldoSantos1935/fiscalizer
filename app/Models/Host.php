<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Host extends Model
{


    protected $table = 'hosts';

    protected $fillable = [
        'nome_conexao',
        'descricao',
        'provedor',
        'tecnologia',
        'ip_atingivel',
        'porta',
        'status',
        'local', // será o id_escola
    ];

    protected $casts = [
        'porta' => 'integer',
        'local' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * 🔹 Relacionamento: cada host pertence a uma escola
     * local → id_escola
     */
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'local', 'id_escola');
    }

    /**
     * 🔹 Escopos de conveniência
     */
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopePorProvedor($query, $provedor)
    {
        return $query->where('provedor', $provedor);
    }

    public function scopePorTecnologia($query, $tecnologia)
    {
        return $query->where('tecnologia', $tecnologia);
    }
}
