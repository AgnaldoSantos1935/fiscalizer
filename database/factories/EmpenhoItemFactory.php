<?php

namespace Database\Factories;

use App\Models\EmpenhoItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmpenhoItem>
 */
class EmpenhoItemFactory extends Factory
{
    protected $model = EmpenhoItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = $this->faker->randomFloat(2, 1, 50);
        $unit = $this->faker->randomFloat(2, 50, 1500);

        return [
            'item_numero' => $this->faker->numberBetween(1, 999),
            'descricao' => $this->faker->sentence(6),
            'unidade' => 'UN',
            'quantidade' => $qty,
            'valor_unitario' => $unit,
            // valor_total é calculado no boot() do modelo
        ];
    }
}
