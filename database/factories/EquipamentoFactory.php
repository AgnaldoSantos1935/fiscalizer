<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipamentoFactory extends Factory
{
    public function definition()
    {
        $tipos = ['desktop', 'notebook', 'servidor', 'switch', 'roteador', 'outro'];
        $origens = ['manual', 'agente', 'importacao'];
        $sistemas = [
            'Windows 10 Pro',
            'Windows 11 Pro',
            'Ubuntu 22.04 LTS',
            'Debian 12',
            'Windows Server 2019',
            'Windows Server 2022',
        ];

        return [
            'serial_number' => strtoupper($this->faker->bothify('SN-####-####')),
            'hostname' => $this->faker->unique()->bothify('PC-###'),
            'sistema_operacional' => $this->faker->randomElement($sistemas),
            'ram_gb' => $this->faker->randomElement([4, 8, 16, 32]),
            'cpu_resumida' => $this->faker->randomElement([
                'Intel i5-8400',
                'Intel i7-8700',
                'Ryzen 5 3600',
                'Intel Xeon E5-2620',
                'Intel i3-10100',
            ]),
            'ip_atual' => $this->faker->ipv4(),
            'discos' => $this->faker->randomElement([
                'SSD 240GB',
                'SSD 480GB',
                'HDD 1TB',
                'M.2 NVMe 512GB',
                '2x SSD 480GB (RAID 1)',
            ]),
            'ultimo_checkin' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'origem_inventario' => $this->faker->randomElement($origens),
            'unidade_id' => (function () {
                $esc = \App\Models\Escola::query()->inRandomOrder()->first();
                if ($esc) {
                    $nome = $esc->escola ?? $esc->nome ?? null;
                    if ($nome) {
                        $uid = \App\Models\Unidade::query()->where('nome', $nome)->value('id');
                        if ($uid) {
                            return $uid;
                        }
                    }
                }

                return \App\Models\Unidade::query()->inRandomOrder()->value('id');
            })(),
            'tipo' => $this->faker->randomElement($tipos),
            'especificacoes' => $this->faker->paragraph(),
        ];
    }
}
