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
        'local',           // FK → escolas.id_escola
        'itemcontratado',  // FK → contrato_itens.id
    ];

    protected $casts = [
        'porta' => 'integer',
        'local' => 'integer',
        'itemcontratado' => 'integer',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * 🏫 Relação com a escola
     * hosts.local → escolas.id_escola
     */
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'local', 'id_escola');
    }

    /**
     * 📦 Relação com o item de contrato
     * hosts.itemcontratado → contrato_itens.id
     */
    public function itemContrato()
    {
        return $this->belongsTo(ContratoItem::class, 'itemcontratado', 'id');
    }

    /**
     * 🔗 Acesso indireto ao contrato via itemContrato
     */
    public function contrato()
    {
        return $this->hasOneThrough(
            Contrato::class,        // modelo final
            ContratoItem::class,    // modelo intermediário
            'id',                   // chave local em contrato_itens
            'id',                   // chave local em contratos
            'itemcontratado',       // FK em hosts
            'contrato_id'           // FK em contrato_itens
        );
    }
}
