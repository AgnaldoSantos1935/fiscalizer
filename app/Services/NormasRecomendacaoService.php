<?php
namespace App\Services;

class NormasRecomendacaoService
{
    public function recomendar(array $spec): array
    {
        $totais = $spec['totais'] ?? [];
        $aps = (int) ($totais['aps'] ?? 0);
        $ports = (int) ($totais['switch_ports'] ?? 0);

        $switchTipo = $ports > 24 ? '48 portas, 4xSFP' : '24 portas, 2xSFP';
        $apTipo = $aps > 0 ? '802.11ax, dual-band, PoE, controlador' : '802.11ac, dual-band';

        $recomendacoes = [
            'computadores' => [
                'descricao' => 'CPU equivalente ao Intel i5 (12ª geração) ou superior; 16 GB RAM DDR4/DDR5; SSD 512 GB NVMe.',
                'justificativa' => 'Desempenho e eficiência para softwares educacionais modernos e acessibilidade adequada.',
                'normas' => [
                    'ISO/IEC 40500 (WCAG) / WCAG 2.1',
                    'ISO/IEC 25010',
                    'NBR ISO/IEC 9126',
                ],
            ],
            'switch' => [
                'descricao' => 'Switch gerenciável, ' . $switchTipo . ', VLAN, QoS, STP, SNMP.',
                'justificativa' => 'Dimensionamento de portas com fator de segurança e segmentação lógica de rede, garantindo desempenho e gestão.',
                'normas' => [
                    'ISO/IEC 11801',
                    'ANSI/TIA-568.2-D',
                    'ANSI/TIA-606-C',
                    'IEEE 802.3ab (Gigabit Ethernet)',
                    'IEEE 802.3an (10GBASE-T)',
                ],
            ],
            'ap' => [
                'descricao' => 'Access Points ' . $apTipo . ', suporte a OFDMA e MU-MIMO.',
                'justificativa' => 'Alta densidade de usuários em ambientes educacionais requer Wi‑Fi de alta capacidade e gerenciamento centralizado.',
                'normas' => [
                    'IEEE 802.11ac/ax',
                    'ABNT NBR ISO/IEC 27001',
                    'ISO/IEC 11801',
                    'NBR 14565',
                    'Resolução ANATEL 680/2017',
                ],
            ],
            'poe' => [
                'descricao' => 'Switches e injetores PoE compatíveis com dispositivos finais.',
                'justificativa' => 'Alimentação dos APs via PoE reduz infraestrutura elétrica adicional e organiza a distribuição de energia.',
                'normas' => [
                    'IEEE 802.3af/at/bt',
                    'ABNT NBR 5410',
                ],
            ],
            'cabeamento' => [
                'descricao' => 'Cabeamento estruturado UTP Cat6, patch panels, tomadas RJ45 e organização por rotas.',
                'justificativa' => 'Atende requisitos de desempenho e administração da infraestrutura com rastreabilidade e manutenção.',
                'normas' => [
                    'ABNT NBR 14565',
                    'ISO/IEC 11801',
                    'ANSI/TIA-606-C',
                    'TIA/EIA-568-B.2-1',
                ],
            ],
            'dimensionamento' => [
                'descricao' => 'Limitar cabo horizontal a 90 m + patch cords até 10 m; topologia em estrela; considerar metragem total por pontos.',
                'justificativa' => 'Garante desempenho e conformidade dimensional do cabeamento estruturado por escola.',
                'normas' => [
                    'NBR 14565',
                    'ISO/IEC 11801-1:2017',
                ],
            ],
            'backbone' => [
                'descricao' => 'Uplink de backbone preferencialmente a 10GbE quando disponível; compatibilidade com agregação.',
                'justificativa' => 'Suporte ao tráfego agregado de APs e laboratórios com margem para expansão.',
                'normas' => [
                    'IEEE 802.3an (10GBASE-T)',
                    'ISO/IEC 11801',
                ],
            ],
            'ups' => [
                'descricao' => 'UPS com onda senoidal pura e autonomia mínima de 10 minutos, com proteção adequada.',
                'justificativa' => 'Mantém serviços críticos durante quedas de energia e melhora a qualidade elétrica.',
                'normas' => [
                    'ABNT NBR 15014',
                    'ISO/IEC 30134-2',
                ],
            ],
            'seguranca' => [
                'descricao' => 'Segregação de redes (Administração × Alunos), VLAN para Laboratórios, WPA2/WPA3-Enterprise, controle centralizado de APs.',
                'justificativa' => 'Fortalece controles de acesso, isolamento de tráfego e governança de TI escolar.',
                'normas' => [
                    'ISO/IEC 27001',
                    'ISO/IEC 27002',
                    'NIST SP 800-53',
                ],
            ],
            'conformidade' => [
                'descricao' => 'Equipamentos com homologação vigente e conformidade regulatória.',
                'justificativa' => 'Assegura conformidade legal e técnica para uso em redes públicas educacionais.',
                'normas' => [
                    'ANATEL Resoluções aplicáveis',
                    'Normas e guias FNDE',
                    'Guia DETEC/SEDUC (quando disponível)',
                ],
            ],
        ];

        return [
            'recomendacoes' => $recomendacoes,
        ];
    }
}
