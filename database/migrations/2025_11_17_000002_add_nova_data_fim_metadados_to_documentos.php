<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('documentos', 'nova_data_fim')) {
                $table->date('nova_data_fim')->nullable()->after('data_upload');
            }
            if (!Schema::hasColumn('documentos', 'metadados')) {
                // Use json if available, fallback to text
                if (method_exists($table, 'json')) {
                    $table->json('metadados')->nullable()->after('nova_data_fim');
                } else {
                    $table->text('metadados')->nullable()->after('nova_data_fim');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            if (Schema::hasColumn('documentos', 'metadados')) {
                $table->dropColumn('metadados');
            }
            if (Schema::hasColumn('documentos', 'nova_data_fim')) {
                $table->dropColumn('nova_data_fim');
            }
        });
    }
};