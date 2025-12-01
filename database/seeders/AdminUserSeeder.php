<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['nome' => 'Administrador'], ['descricao' => 'Acesso total']);

        $user = User::firstOrNew(['email' => 'agnaldosantos1935@gmail.com']);
        $user->name = 'AGNALDO SANTOS';
        $user->password = 'S@n#t0s.100';
        $user->role_id = $role->id;
        $user->save();
    }
}
