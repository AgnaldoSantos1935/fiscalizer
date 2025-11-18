<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email}';

    protected $description = 'Gera uma nova senha provisória para um usuário';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error('Usuário não encontrado.');

            return;
        }

        $newPassword = Str::random(10);
        $user->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => true,
            'password_expires_at' => now()->addDays(7),
        ]);

        $this->info("Nova senha provisória: {$newPassword}");
    }
}
