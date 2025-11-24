<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_events', function (Blueprint $table) {
            $table->string('priority')->default('normal')->after('enabled');
            $table->string('recipient_scope')->default('intersection')->after('priority');
            $table->json('recipient_roles')->nullable()->after('recipient_scope');
            $table->json('recipient_users')->nullable()->after('recipient_roles');
            $table->boolean('should_generate')->default(true)->after('recipient_users');
            $table->json('rules')->nullable()->after('should_generate');
            $table->json('workflow')->nullable()->after('rules');
        });
    }

    public function down(): void
    {
        Schema::table('notification_events', function (Blueprint $table) {
            $table->dropColumn([
                'priority',
                'recipient_scope',
                'recipient_roles',
                'recipient_users',
                'should_generate',
                'rules',
                'workflow',
            ]);
        });
    }
};
