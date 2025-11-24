<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

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

