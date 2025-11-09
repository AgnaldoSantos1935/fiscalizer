<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();

            // 🔹 Informações gerais da conexão
            $table->string('nome_conexao', 255)->comment('Nome identificador da conexão, ex: Link Starlink Escola X');
            $table->string('descricao', 255)->nullable()->comment('Descrição ou observações sobre a conexão');

            // 🔹 Dados técnicos
            $table->string('provedor', 100)->nullable()->comment('Provedor responsável pelo link, ex: Starlink, Vivo');
            $table->string('tecnologia', 50)->nullable()->comment('Tipo de tecnologia: fibra, rádio, satélite, 4G etc.');
            $table->string('ip_atingivel', 45)->nullable()->comment('Endereço IP testável');
            $table->integer('porta')->nullable()->comment('Porta usada para verificação de conectividade');

            // 🔹 Status da conexão
            $table->enum('status', ['ativo', 'inativo', 'em manutenção'])->default('ativo')
                ->comment('Status operacional do link');

            // 🔹 Relacionamentos
            $table->unsignedBigInteger('local')->nullable()->comment('Chave estrangeira: escolas.id_escola');
            $table->unsignedBigInteger('itemcontratado')->nullable()->comment('Chave estrangeira: contrato_itens.id');

            // 🔹 Datas padrão
            $table->timestamps();

            // 🔹 Índices
            $table->index('provedor');
            $table->index('tecnologia');
            $table->index('status');
            $table->index('local');
            $table->index('itemcontratado');

            // 🔹 Chaves estrangeiras
            $table->foreign('local')
                ->references('id_escola')
                ->on('escolas')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('itemcontratado')
                ->references('id')
                ->on('contrato_itens')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosts');
    }
};
