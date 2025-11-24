<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->string('emitido_pdf_path')->nullable()->after('valor_total');
            $table->timestamp('emitido_at')->nullable()->after('emitido_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->dropColumn(['emitido_pdf_path', 'emitido_at']);
        });
    }
};
