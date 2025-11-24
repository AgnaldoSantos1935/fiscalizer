<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            if (! Schema::hasColumn('empenhos', 'pago_comprovante_path')) {
                $table->string('pago_comprovante_path')->nullable()->after('emitido_at');
            }
            if (! Schema::hasColumn('empenhos', 'pago_at')) {
                $table->timestamp('pago_at')->nullable()->after('pago_comprovante_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->dropColumn(['pago_comprovante_path', 'pago_at']);
        });
    }
};
