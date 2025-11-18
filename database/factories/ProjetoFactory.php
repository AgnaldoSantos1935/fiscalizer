<?php

namespace Database\Factories;

use App\Models\Contrato;
use App\Models\Projeto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjetoFactory extends Factory
{
    protected $model = Projeto::class;

    public function definition(): array
    {
        return [
            'contrato_id' => Contrato::inRandomOrder()->value('id') ?? Contrato::factory(),
            'nome' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['planejado', 'em_execucao', 'concluido', 'suspenso']),
            'data_inicio' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'data_fim' => $this->faker->optional(0.5)->dateTimeBetween('now', '+6 months'),
            'created_by' => 1, // opcionalmente substitua por auth()->id() se usar seeding com login
        ];
    }
}
