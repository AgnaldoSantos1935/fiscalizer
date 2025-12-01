<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dres')) {
            return;
        }
        Schema::table('dres', function (Blueprint $table) {
            if (! Schema::hasColumn('dres', 'cep')) {
                $table->string('cep', 9)->nullable()->after('endereco');
            }
            if (! Schema::hasColumn('dres', 'logradouro')) {
                $table->string('logradouro', 200)->nullable()->after('cep');
            }
            if (! Schema::hasColumn('dres', 'numero')) {
                $table->string('numero', 20)->nullable()->after('logradouro');
            }
            if (! Schema::hasColumn('dres', 'complemento')) {
                $table->string('complemento', 150)->nullable()->after('numero');
            }
            if (! Schema::hasColumn('dres', 'bairro')) {
                $table->string('bairro', 100)->nullable()->after('complemento');
            }
            if (! Schema::hasColumn('dres', 'uf')) {
                $table->string('uf', 2)->nullable()->after('municipio_sede');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('dres')) {
            return;
        }
        Schema::table('dres', function (Blueprint $table) {
            foreach (['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'uf'] as $col) {
                if (Schema::hasColumn('dres', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
