<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class AtividadeTecnica extends Model
{
protected $table = 'atividades_tecnicas';
protected $fillable = ['apf_id','etapa','descricao','horas_trabalhadas','analista','data'];
protected $casts = ['data' => 'date'];
public function apf(){ return $this->belongsTo(Apf::class, 'apf_id'); }
}
