<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ata_adesoes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ata_id')->constrained('atas_registro_precos')->onDelete('cascade');
            $t->foreignId('orgao_adquirente_id')->nullable()->constrained('empresas')->nullOnDelete();
            $t->text('justificativa')->nullable();
            $t->enum('status', ['solicitada', 'autorizada', 'negada', 'cancelada'])->default('solicitada');
            $t->string('documento_pdf_path')->nullable();
            $t->decimal('valor_estimado', 18, 2)->nullable();
            $t->date('data_solicitacao')->nullable();
            $t->date('data_decisao')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ata_adesoes');
    }
};
