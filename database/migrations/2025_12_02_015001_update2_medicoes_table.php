<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('medicoes')) {
            Schema::table('medicoes', function (Blueprint $table) {
                if (! Schema::hasColumn('medicoes', 'ano_referencia')) {
                    $table->integer('ano_referencia')->nullable()->after('mes_referencia');
                }
            });
        } else {
            Schema::create('medicoes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('contrato_id')
                    ->constrained('contratos')
                    ->onDelete('cascade');

                $table->integer('mes_referencia');
                $table->integer('ano_referencia');

                $table->foreignId('created_by')->nullable()->constrained('users');

                $table->timestamps();

                $table->unique(['contrato_id', 'mes_referencia', 'ano_referencia'], 'medicao_unica_mes');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('medicoes')) {
            if (Schema::hasColumn('medicoes', 'ano_referencia')) {
                Schema::table('medicoes', function (Blueprint $table) {
                    $table->dropColumn('ano_referencia');
                });
            }
        }
    }
};
