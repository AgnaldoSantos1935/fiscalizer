<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_profiles') && ! Schema::hasColumn('user_profiles', 'email_institucional')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->string('email_institucional')->nullable()->after('celular');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('user_profiles', 'email_institucional')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->dropColumn('email_institucional');
            });
        }
    }
};

