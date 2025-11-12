<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Apf extends Model
{
protected $table = 'apfs';
protected $fillable = [
'projeto_id','numero','tipo','pontos_funcao','data_abertura','data_homologacao','status','item_contrato_id'
];
protected $casts = [ 'data_abertura' => 'date','data_homologacao' => 'date','pontos_funcao' => 'decimal:2' ];


public function projeto(){ return $this->belongsTo(ProjetoSoftware::class, 'projeto_id'); }
public function atividades(){ return $this->hasMany(AtividadeTecnica::class, 'apf_id'); }
public function fiscalizacoes(){ return $this->hasMany(FiscalizacaoProjeto::class, 'apf_id'); }
public function itemContrato(){ return $this->belongsTo(ItemContrato::class, 'item_contrato_id'); }
}
