<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // user_profiles — adiciona e ajusta colunas para captura automática via CEP
        if (Schema::hasTable('user_profiles')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('user_profiles', 'logradouro')) {
                    $table->string('logradouro')->nullable()->after('endereco');
                }
                if (! Schema::hasColumn('user_profiles', 'numero')) {
                    $table->string('numero')->nullable()->after('logradouro');
                }
                if (! Schema::hasColumn('user_profiles', 'complemento')) {
                    $table->string('complemento')->nullable()->after('numero');
                }
                if (Schema::hasColumn('user_profiles', 'bairro')) {
                    $table->string('bairro')->nullable()->change();
                } else {
                    $table->string('bairro')->nullable()->after('complemento');
                }
                if (Schema::hasColumn('user_profiles', 'cidade')) {
                    $table->string('cidade')->nullable()->change();
                } else {
                    $table->string('cidade')->nullable()->after('bairro');
                }
                if (! Schema::hasColumn('user_profiles', 'uf')) {
                    $table->string('uf', 2)->nullable()->after('cidade');
                }
                if (Schema::hasColumn('user_profiles', 'cep')) {
                    $table->string('cep', 9)->nullable()->change(); // 00000-000
                } else {
                    $table->string('cep', 9)->nullable()->after('uf');
                }
            });
        }

        // escolas — padroniza estrutura de endereço detalhado
        if (Schema::hasTable('escolas')) {
            Schema::table('escolas', function (Blueprint $table) {
                if (! Schema::hasColumn('escolas', 'cep')) {
                    $table->string('cep', 9)->nullable()->after('uf');
                }
                if (! Schema::hasColumn('escolas', 'logradouro')) {
                    $table->string('logradouro')->nullable()->after('cep');
                }
                if (! Schema::hasColumn('escolas', 'numero')) {
                    $table->string('numero')->nullable()->after('logradouro');
                }
                if (! Schema::hasColumn('escolas', 'complemento')) {
                    $table->string('complemento')->nullable()->after('numero');
                }
                if (! Schema::hasColumn('escolas', 'bairro')) {
                    $table->string('bairro')->nullable()->after('complemento');
                }
                // mantém 'municipio' existente; não cria 'cidade' para evitar duplicidade
            });
        }

        // dres — adiciona colunas detalhadas e UF
        if (Schema::hasTable('dres')) {
            Schema::table('dres', function (Blueprint $table) {
                if (! Schema::hasColumn('dres', 'cep')) {
                    $table->string('cep', 9)->nullable()->after('telefone');
                }
                if (! Schema::hasColumn('dres', 'logradouro')) {
                    $table->string('logradouro')->nullable()->after('cep');
                }
                if (! Schema::hasColumn('dres', 'numero')) {
                    $table->string('numero')->nullable()->after('logradouro');
                }
                if (! Schema::hasColumn('dres', 'complemento')) {
                    $table->string('complemento')->nullable()->after('numero');
                }
                if (! Schema::hasColumn('dres', 'bairro')) {
                    $table->string('bairro')->nullable()->after('complemento');
                }
                if (! Schema::hasColumn('dres', 'uf')) {
                    $table->string('uf', 2)->nullable()->after('municipio_sede');
                }
            });
        }
    }

    public function down(): void
    {
        // Remoção apenas das colunas adicionadas; não reverte alterações de tamanho
        if (Schema::hasTable('user_profiles')) {
            Schema::table('user_profiles', function (Blueprint $table) {
                foreach (['logradouro', 'numero', 'complemento', 'uf'] as $col) {
                    if (Schema::hasColumn('user_profiles', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('escolas')) {
            Schema::table('escolas', function (Blueprint $table) {
                foreach (['cep', 'logradouro', 'numero', 'complemento', 'bairro'] as $col) {
                    if (Schema::hasColumn('escolas', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('dres')) {
            Schema::table('dres', function (Blueprint $table) {
                foreach (['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'uf'] as $col) {
                    if (Schema::hasColumn('dres', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};