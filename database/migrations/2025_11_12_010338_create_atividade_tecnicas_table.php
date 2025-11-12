<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('atividades_tecnicas', function (Blueprint $table) {
$table->id();
$table->unsignedBigInteger('apf_id');
$table->enum('etapa', ['Analise','Prototipacao','Desenvolvimento','Teste','Homologacao','Implantacao']);
$table->text('descricao')->nullable();
$table->integer('horas_trabalhadas')->nullable();
$table->string('analista')->nullable();
$table->date('data')->nullable();
$table->timestamps();
$table->foreign('apf_id')->references('id')->on('apfs')->cascadeOnDelete();
});
}
public function down(): void { Schema::dropIfExists('atividades_tecnicas'); }
};
