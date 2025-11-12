<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class ProjetoSoftware extends Model
{
protected $table = 'projetos_software';
protected $fillable = [
'codigo','titulo','sistema','modulo','submodulo','solicitante','fornecedor',
'pontos_funcao','data_solicitacao','data_homologacao','situacao','valor_estimado','contrato_id'
];


protected $casts = [
'data_solicitacao' => 'date',
'data_homologacao' => 'date',
'pontos_funcao' => 'decimal:2',
'valor_estimado' => 'decimal:2',
];


public function contrato(){ return $this->belongsTo(Contrato::class); }
public function apfs(){ return $this->hasMany(Apf::class, 'projeto_id'); }
public function fiscalizacoes(){ return $this->hasMany(FiscalizacaoProjeto::class, 'projeto_id'); }
}
