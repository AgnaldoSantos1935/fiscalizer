<?php

namespace Database\Seeders;

use App\Models\Equipamento;
use App\Models\Escola;
use App\Models\Unidade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class EquipamentoSeeder extends Seeder
{
    public function run()
    {
        if (Unidade::count() < 10) {
            $escolas = Escola::query()->inRandomOrder()->limit(10)->get();
            foreach ($escolas as $e) {
                $nome = $e->escola ?? $e->nome ?? null;
                $tel = $e->telefone ?? null;
                if ($nome) {
                    Unidade::firstOrCreate(
                        ['nome' => $nome],
                        ['tipo' => 'Escola', 'telefone' => $tel]
                    );
                }
            }
        }
        // 🔹 Desktops
        Equipamento::factory()->count(20)->state([
            'tipo' => 'desktop',
            'sistema_operacional' => 'Windows 10 Pro',
            'ram_gb' => 8,
            'cpu_resumida' => 'Intel i5-8400',
        ])->create();

        // 🔹 Notebooks
        Equipamento::factory()->count(15)->state([
            'tipo' => 'notebook',
            'sistema_operacional' => 'Windows 11 Pro',
            'ram_gb' => 8,
            'cpu_resumida' => 'Intel i5-10210U',
        ])->create();

        // 🔹 Servidores
        Equipamento::factory()->count(5)->state([
            'tipo' => 'servidor',
            'sistema_operacional' => 'Windows Server 2019',
            'ram_gb' => 32,
            'cpu_resumida' => 'Intel Xeon E5-2620',
            'discos' => '2x SSD 480GB (RAID 1)',
        ])->create();

        // 🔹 Switches
        Equipamento::factory()->count(8)->state([
            'tipo' => 'switch',
            'sistema_operacional' => 'Embedded OS',
            'ram_gb' => null,
            'cpu_resumida' => 'Chipset Realtek',
            'discos' => null,
        ])->create();

        // 🔹 Roteadores
        Equipamento::factory()->count(6)->state([
            'tipo' => 'roteador',
            'sistema_operacional' => 'RouterOS 7.8',
            'ram_gb' => null,
            'cpu_resumida' => 'MikroTik dual-core',
            'discos' => null,
        ])->create();

        // 🔹 Outros
        Equipamento::factory()->count(6)->state([
            'tipo' => 'outro',
            'sistema_operacional' => '—',
        ])->create();

        $unidades = Unidade::query()->get(['id', 'nome']);
        if ($unidades->isNotEmpty()) {
            $nameCol = Schema::hasColumn('escolas', 'escola') ? 'escola' : (Schema::hasColumn('escolas', 'nome') ? 'nome' : 'escola');
            $escolas = Escola::query()
                ->whereIn($nameCol, $unidades->pluck('nome')->all())
                ->get([$nameCol, 'dre', 'municipio']);

            $grupos = [];
            foreach ($unidades as $u) {
                $esc = $escolas->firstWhere($nameCol, $u->nome);
                $chave = $esc && $esc->dre ? $esc->dre : ($esc && $esc->municipio ? $esc->municipio : 'desconhecido');
                $grupos[$chave] = $grupos[$chave] ?? [];
                $grupos[$chave][] = $u->id;
            }

            ksort($grupos);

            $idsDistribuicao = [];
            foreach ($grupos as $idsGrupo) {
                foreach ($idsGrupo as $id) {
                    $idsDistribuicao[] = $id;
                }
            }

            if (! empty($idsDistribuicao)) {
                $i = 0;
                Equipamento::whereNull('unidade_id')
                    ->orderBy('id')
                    ->chunkById(200, function ($equipamentos) use (&$i, $idsDistribuicao) {
                        foreach ($equipamentos as $eq) {
                            $eq->unidade_id = $idsDistribuicao[$i % count($idsDistribuicao)];
                            $eq->save();
                            $i++;
                        }
                    });
            }
        }
    }
}
