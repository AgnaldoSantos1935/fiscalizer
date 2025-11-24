<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContratoFiscalSeeder extends Seeder
{
    /**
     * Vincula usuários como fiscais (técnico, administrativo e gestor) aos contratos.
     */
    public function run(): void
    {
        // Pools por tipo (se existirem roles específicas, usa-as; caso contrário, usa todos os usuários)
        $usuarios = User::query()->get();
        if ($usuarios->isEmpty()) {
            // Garante usuários mínimos para vincular
            $usuarios = collect([
                User::query()->create(['name' => 'Fiscal Técnico', 'email' => 'fiscal.tecnico@example.com', 'password' => bcrypt('password')]),
                User::query()->create(['name' => 'Fiscal Administrativo', 'email' => 'fiscal.administrativo@example.com', 'password' => bcrypt('password')]),
                User::query()->create(['name' => 'Gestor do Contrato', 'email' => 'gestor.contrato@example.com', 'password' => bcrypt('password')]),
            ]);
        }

        // Tenta obter grupos por role
        $poolTecnico = $this->poolPorRole('Fiscal Técnico', $usuarios);
        $poolAdministrativo = $this->poolPorRole('Fiscal Administrativo', $usuarios);
        $poolGestor = $this->poolPorRole('Gestor', $usuarios);

        // Fallback: se algum pool ficou vazio, usa o conjunto geral
        if ($poolTecnico->isEmpty()) {
            $poolTecnico = $usuarios;
        }
        if ($poolAdministrativo->isEmpty()) {
            $poolAdministrativo = $usuarios;
        }
        if ($poolGestor->isEmpty()) {
            $poolGestor = $usuarios;
        }

        $contratos = DB::table('contratos')->get();
        if ($contratos->isEmpty()) {
            return; // nada a fazer
        }

        foreach ($contratos as $contrato) {
            // Vincula um usuário como fiscal técnico, se ainda não tiver pelo pivô
            $hasTecnico = DB::table('contrato_fiscais')
                ->where('contrato_id', $contrato->id)
                ->where('tipo', 'fiscal_tecnico')
                ->exists();
            if (! $hasTecnico) {
                $user = $this->pickByContrato($poolTecnico, $contrato->id);
                DB::table('contrato_fiscais')->insert([
                    'contrato_id' => $contrato->id,
                    'user_id' => $user->id,
                    'tipo' => 'fiscal_tecnico',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Vincula um usuário como fiscal administrativo
            $hasAdm = DB::table('contrato_fiscais')
                ->where('contrato_id', $contrato->id)
                ->where('tipo', 'fiscal_administrativo')
                ->exists();
            if (! $hasAdm) {
                $user = $this->pickByContrato($poolAdministrativo, $contrato->id + 1);
                DB::table('contrato_fiscais')->insert([
                    'contrato_id' => $contrato->id,
                    'user_id' => $user->id,
                    'tipo' => 'fiscal_administrativo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Vincula um usuário como gestor
            $hasGestor = DB::table('contrato_fiscais')
                ->where('contrato_id', $contrato->id)
                ->where('tipo', 'gestor')
                ->exists();
            if (! $hasGestor) {
                $user = $this->pickByContrato($poolGestor, $contrato->id + 2);
                DB::table('contrato_fiscais')->insert([
                    'contrato_id' => $contrato->id,
                    'user_id' => $user->id,
                    'tipo' => 'gestor',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function poolPorRole(string $roleNome, $fallbackUsuarios)
    {
        $role = Role::query()->where('nome', $roleNome)->first();

        return $role ? $role->users()->get() : collect($fallbackUsuarios);
    }

    private function pickByContrato($pool, int $seedIndex): User
    {
        $pool = $pool->values();
        $idx = $seedIndex % max(1, $pool->count());

        return $pool[$idx];
    }
}
