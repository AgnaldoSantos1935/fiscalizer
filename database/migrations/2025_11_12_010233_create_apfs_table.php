<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('apfs', function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('projeto_id');
$table->string('numero', 40); // 003/2025, etc.
$table->string('tipo')->default('Melhoria/Implementacao'); // livre ou enum
$table->decimal('pontos_funcao', 8, 2)->nullable();
$table->date('data_abertura')->nullable();
$table->date('data_homologacao')->nullable();
$table->enum('status', ['Aberta','Em Analise','Em Desenvolvimento','Homologada','Reprovada','Encerrada'])->default('Aberta');
$table->unsignedBigInteger('item_contrato_id')->nullable();
$table->foreign('projeto_id')->references('id')->on('projetos_software')->cascadeOnDelete();
$table->foreign('item_contrato_id')->references('id')->on('itens_contrato')->nullOnDelete();
$table->timestamps();
$table->unique(['projeto_id','numero']);
});
}
public function down(): void { Schema::dropIfExists('apfs'); }
};
