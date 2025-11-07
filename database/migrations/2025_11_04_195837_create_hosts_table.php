<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            // garante que o campo é inteiro e relacionável
            $table->unsignedBigInteger('local')->change();

            // cria a foreign key (se ainda não existir)
            $table->foreign('local')->references('id_escola')->on('escolas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            $table->dropForeign(['local']);
        });
    }
};
