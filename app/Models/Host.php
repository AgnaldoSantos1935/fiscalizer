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
        'local',
        'itemcontratado',

        // ---- NOVOS CAMPOS PARA MONITORAMENTO ----
        'tipo_monitoramento',   // ping, porta, http, snmp, mikrotik, speedtest
        'host_alvo',            // IP ou URL monitorado
        'snmp_community',       // community SNMP
        'mikrotik_user',        // usuário Mikrotik
        'mikrotik_pass',        // senha Mikrotik
        'config_extra',         // JSON com configurações adicionais
    ];

    protected $casts = [
        'porta'          => 'integer',
        'local'          => 'integer',
        'itemcontratado' => 'integer',
        'config_extra'   => 'array',   // <-- importante!
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * 🏫 Escola onde o host está localizado
     */
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'local', 'id_escola');
    }

    /**
     * 📦 Item de contrato que originou o host
     */
    public function itemContrato()
    {
        return $this->belongsTo(ContratoItem::class, 'itemcontratado', 'id');
    }

    /**
     * 🔍 Histórico de monitoramentos do host
     */
    public function monitoramentos()
    {
        return $this->hasMany(Monitoramento::class, 'host_id');
    }

    /**
     * 📑 Contrato atrelado ao item contratado
     */
    public function contrato()
    {
        return $this->hasOneThrough(
            Contrato::class,
            ContratoItem::class,
            'id',           // FK em contrato_itens
            'id',           // PK em contratos
            'itemcontratado',
            'contrato_id'   // FK em contrato_itens
        );
    }
    public function indisponibilidades()
{
    return $this->hasMany(Indisponibilidade::class, 'host_id');
}

}
