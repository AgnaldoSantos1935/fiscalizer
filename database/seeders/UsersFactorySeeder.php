<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersFactorySeeder extends Seeder
{
    public function run(): void
    {
        $total = 200;
        $proportions = [
            'Fiscal' => 0.40,
            'fiscal_tecnico' => 0.20,
            'fiscal_administrativo' => 0.20,
            'Gestor de Contrato' => 0.15,
            'Administrador' => 0.05,
        ];

        $roles = Role::query()->whereIn('nome', array_keys($proportions))->pluck('id', 'nome');

        $existing = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.nome', array_keys($proportions))
            ->selectRaw('roles.nome, COUNT(*) as c')
            ->groupBy('roles.nome')
            ->pluck('c', 'roles.nome');

        $allocated = 0;
        $names = array_keys($proportions);
        $last = end($names);
        foreach ($proportions as $nome => $pct) {
            $qtd = $nome === $last ? ($total - $allocated) : (int) round($total * $pct);
            $desired = $qtd;
            $current = (int) ($existing[$nome] ?? 0);
            $missing = max(0, $desired - $current);
            $allocated += $desired;
            $roleId = $roles[$nome] ?? null;
            if (! $roleId || $missing <= 0) {
                continue;
            }
            User::factory()->count($missing)->create([
                'role_id' => $roleId,
            ]);
        }

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User']
        );
    }
}
