<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Se a coluna existir, antes de remover, sincroniza para users.email quando aplicável
        if (Schema::hasColumn('user_profiles', 'email_institucional')) {
            $profiles = DB::table('user_profiles')
                ->select('user_id', 'email_institucional')
                ->whereNotNull('email_institucional')
                ->get();

            foreach ($profiles as $p) {
                if (! empty($p->email_institucional)) {
                    DB::table('users')
                        ->where('id', $p->user_id)
                        ->whereNull('email')
                        ->update(['email' => $p->email_institucional]);
                }
            }

            Schema::table('user_profiles', function (Blueprint $table) {
                $table->dropColumn('email_institucional');
            });
        }
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('user_profiles', 'email_institucional')) {
                $table->string('email_institucional')->nullable();
            }
        });

        // Opcional: repopula com users.email
        $users = DB::table('users')
            ->select('id', 'email')
            ->whereNotNull('email')
            ->get();

        foreach ($users as $u) {
            if (! empty($u->email)) {
                DB::table('user_profiles')
                    ->where('user_id', $u->id)
                    ->update(['email_institucional' => $u->email]);
            }
        }
    }
};
