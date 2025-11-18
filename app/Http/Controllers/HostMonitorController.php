<?php

namespace App\Http\Controllers;

use App\Models\Host;

class HostMonitorController extends Controller
{
    /**
     * Endpoint consumido pelo script Python.
     * Retorna apenas hosts ativos e com os campos necessários.
     */
    public function listarHosts()
    {
        return Host::where('status', 1)  // ou 'ativo' se usar boolean
            ->select(
                'id',
                'nome_conexao',
                'descricao',
                'provedor',
                'tecnologia',

                // Campos essenciais
                'tipo_monitoramento',
                'host_alvo',
                'porta',

                // Credenciais Mikrotik
                'mikrotik_user',
                'mikrotik_pass',

                // SNMP
                'snmp_community',

                // Config extra para novos recursos de monitoramento
                'config_extra'
            )
            ->get();
    }
}
