<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documento_tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->boolean('permite_nova_data_fim')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Seed básico de tipos conhecidos
        DB::table('documento_tipos')->insert([
            ['nome' => 'PDF do Contrato', 'slug' => 'contrato_pdf', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Termo Aditivo (Prazo)', 'slug' => 'termo_aditivo_prazo', 'descricao' => null, 'permite_nova_data_fim' => true, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Termo Aditivo (Valor)', 'slug' => 'termo_aditivo_valor', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Termo de Referência', 'slug' => 'TR', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Estudo Técnico Preliminar', 'slug' => 'ETP', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Parecer', 'slug' => 'PARECER', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Nota Técnica', 'slug' => 'NOTA_TECNICA', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Relatório', 'slug' => 'RELATORIO', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Outros', 'slug' => 'OUTROS', 'descricao' => null, 'permite_nova_data_fim' => false, 'ativo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_tipos');
    }
};