<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class ContratoFactory extends Factory
{
    public function definition()
    {
        $inicio = $this->faker->dateTimeBetween('-2 years', 'now');
        $fim = (clone $inicio)->modify('+1 year');

        return [
            'numero' => $this->faker->unique()->numerify('065/20##'),
            'objeto' => 'Prestação de serviços de tecnologia da informação - ' . $this->faker->bs(),
            'contratada_id' => \App\Models\Empresa::inRandomOrder()->first()->id ?? 1,
            'valor_global' => $this->faker->randomFloat(2, 100000, 5000000),
            'data_inicio' => $inicio,
            'data_fim' => $fim,
            'situacao' => $this->faker->randomElement(['vigente', 'encerrado', 'rescindido']),
            'gestor_id' => null,
            'fiscal_id' => null,
            'tipo' => $this->faker->randomElement(['TI', 'Serviço', 'Obra', 'Material']),
        ];
    }
}
