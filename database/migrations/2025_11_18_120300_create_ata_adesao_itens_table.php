<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ata_adesao_itens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('adesao_id')->constrained('ata_adesoes')->onDelete('cascade');
            $t->foreignId('ata_item_id')->constrained('ata_itens')->onDelete('cascade');
            $t->decimal('quantidade', 18, 2)->default(0);
            $t->decimal('valor_unitario', 18, 4)->default(0);
            $t->decimal('valor_total', 18, 2)->default(0);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ata_adesao_itens');
    }
};
