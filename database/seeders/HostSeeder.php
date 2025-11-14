<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HostSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hosts')->insert([
            // 🔹 PING – Google DNS
            [
                'nome_conexao'      => 'Google DNS',
                'descricao'         => 'Serviço de DNS público',
                'provedor'          => 'Google',
                'tecnologia'        => 'Internet',
                'ip_atingivel'      => '8.8.8.8',
                'porta'             => null,
                'status'            => 1,
                'tipo_monitoramento'=> 'ping',
                'host_alvo'         => '8.8.8.8',
                'snmp_community'    => null,
                'mikrotik_user'     => null,
                'mikrotik_pass'     => null,
                'config_extra'      => null,
            ],

            // 🔹 TESTE DE PORTA – Servidor Web HTTPS
            [
                'nome_conexao'      => 'Servidor Web HTTPS',
                'descricao'         => 'Teste na porta 443',
                'provedor'          => 'Cloudflare',
                'tecnologia'        => 'HTTPS',
                'ip_atingivel'      => '1.1.1.1',
                'porta'             => 443,
                'status'            => 1,
                'tipo_monitoramento'=> 'porta',
                'host_alvo'         => '1.1.1.1',
                'snmp_community'    => null,
                'mikrotik_user'     => null,
                'mikrotik_pass'     => null,
                'config_extra'      => null,
            ],

            // 🔹 TESTE HTTP – SEDUC site
            [
                'nome_conexao'      => 'Portal SEDUC',
                'descricao'         => 'Página inicial',
                'provedor'          => 'SEDUC/PA',
                'tecnologia'        => 'HTTP',
                'ip_atingivel'      => 'www.seduc.pa.gov.br',
                'porta'             => 80,
                'status'            => 1,
                'tipo_monitoramento'=> 'http',
                'host_alvo'         => 'https://www.seduc.pa.gov.br',
                'snmp_community'    => null,
                'mikrotik_user'     => null,
                'mikrotik_pass'     => null,
                'config_extra'      => json_encode([
                    'timeout' => 5
                ]),
            ],

            // 🔹 SNMP – Switch HP/L3
            [
                'nome_conexao'      => 'Switch Core HP',
                'descricao'         => 'Switch L3 SEDUC/PA',
                'provedor'          => 'HP Aruba',
                'tecnologia'        => 'SNMP v2c',
                'ip_atingivel'      => '10.0.0.254',
                'porta'             => 161,
                'status'            => 1,
                'tipo_monitoramento'=> 'snmp',
                'host_alvo'         => '10.0.0.254',
                'snmp_community'    => 'public',
                'mikrotik_user'     => null,
                'mikrotik_pass'     => null,
                'config_extra'      => json_encode([
                    'oids' => [
                        'cpu' => '1.3.6.1.4.1.11.2.3.1.1.5.1.0',
                        'mem' => '1.3.6.1.4.1.11.2.3.1.1.12.1.0'
                    ]
                ]),
            ],

            // 🔹 MIKROTIK – Core CCR SEDUC
            [
                'nome_conexao'      => 'Core CCR SEDUC',
                'descricao'         => 'Roteador principal',
                'provedor'          => 'SEDUC',
                'tecnologia'        => 'Mikrotik CCR',
                'ip_atingivel'      => '10.10.20.1',
                'porta'             => 8728,
                'status'            => 1,
                'tipo_monitoramento'=> 'mikrotik',
                'host_alvo'         => '10.10.20.1',
                'snmp_community'    => null,
                'mikrotik_user'     => 'admin',
                'mikrotik_pass'     => 'senha123',
                'config_extra'      => json_encode([
                    'interfaces' => ['ether1', 'ether2'],
                ]),
            ],
        ]);
    }
}
