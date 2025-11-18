<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EscolaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->numerify('ESC-###'),
            'restricao_atendimento' => $this->faker->randomElement(['Nenhuma', 'Parcial', 'Total']),
            'nome' => 'Escola '.$this->faker->company(),
            'codigo_inep' => $this->faker->numerify('########'),
            'uf' => $this->faker->stateAbbr(),
            'municipio' => $this->faker->city(),
            'localizacao' => $this->faker->randomElement(['Urbana', 'Rural']),
            'localidade_diferenciada' => $this->faker->randomElement(['Nenhuma', 'Indígena', 'Quilombola']),
            'categoria_administrativa' => $this->faker->randomElement(['Pública', 'Privada']),
            'endereco' => $this->faker->address(),
            'telefone' => $this->faker->phoneNumber(),
            'dependencia_administrativa' => $this->faker->randomElement(['Estadual', 'Municipal', 'Federal']),
            'categoria_escola_privada' => $this->faker->randomElement(['Sem fins lucrativos', 'Com fins lucrativos']),
            'conveniada_poder_publico' => $this->faker->randomElement(['Sim', 'Não']),
            'regulamentacao_conselho' => $this->faker->randomElement(['Sim', 'Não']),
            'porte' => $this->faker->randomElement(['Pequeno', 'Médio', 'Grande']),
            'etapas_modalidades' => 'Ensino Fundamental, Ensino Médio',
            'outras_ofertas' => 'EJA, Educação Especial',
            'latitude' => $this->faker->latitude(-5, -1),
            'longitude' => $this->faker->longitude(-55, -47),
        ];
    }
}
