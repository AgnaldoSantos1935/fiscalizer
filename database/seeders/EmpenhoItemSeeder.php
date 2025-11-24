<?php

namespace Database\Seeders;

use App\Models\Empenho;
use App\Models\EmpenhoItem;
use Illuminate\Database\Seeder;

class EmpenhoItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empenhos = Empenho::query()->get();
        foreach ($empenhos as $empenho) {
            // Se já tiver itens, não duplicar
            if (EmpenhoItem::query()->where('nota_empenho_id', $empenho->id)->exists()) {
                continue;
            }

            $count = random_int(2, 6);
            EmpenhoItem::factory()
                ->count($count)
                ->create([
                    'nota_empenho_id' => $empenho->id,
                ]);

            // Recalcular valor_total do empenho pela soma dos itens
            $total = EmpenhoItem::query()
                ->where('nota_empenho_id', $empenho->id)
                ->sum('valor_total');

            $empenho->updateQuietly(['valor_total' => $total]);
        }
    }
}
