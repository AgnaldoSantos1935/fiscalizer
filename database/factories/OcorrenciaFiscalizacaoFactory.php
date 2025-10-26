<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class OcorrenciaFiscalizacaoFactory extends Factory
{
    public function definition()
    {
        return [
            'fiscalizacao_id' => 1, // se tiver tabela fiscalizacoes, pode sortear
            'tipo' => $this->faker->randomElement(['SLA', 'Atraso', 'Falha técnica']),
            'descricao' => $this->faker->sentence(10),
            'data_ocorrencia' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'status' => $this->faker->randomElement(['pendente', 'em_analise', 'resolvido']),
            'responsavel' => 1,
        ];
    }
}

