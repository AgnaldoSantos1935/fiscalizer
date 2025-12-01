<?php

use App\Models\Unidade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventarioTokenSeeder extends Seeder
{
    public function run()
    {
        Unidade::whereNull('inventario_token')
            ->chunkById(100, function ($unidades) {
                foreach ($unidades as $u) {
                    $u->inventario_token = Str::uuid()->toString();
                    $u->save();
                }
            });
    }
}
