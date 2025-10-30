<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razao_social' => $this->faker->company(),
            'nome_fantasia' => $this->faker->companySuffix(),
            'cnpj' => $this->faker->unique()->numerify('##.###.###/####-##'),
            'inscricao_estadual' => $this->faker->numerify('#########'),
            'email' => $this->faker->companyEmail(),
            'telefone' => $this->faker->phoneNumber(),
            'endereco' => $this->faker->address(),
            'cidade' => $this->faker->city(),
            'uf' => $this->faker->stateAbbr(),
            'cep' => $this->faker->numerify('#####-###'),
        ];
    }
}
