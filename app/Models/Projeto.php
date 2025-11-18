<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    protected $fillable = [
        'codigo', 'titulo', 'descricao', 'sistema', 'modulo',
        'contrato_id', 'itemcontrato_id',
        'gerente_tecnico_id', 'gerente_adm_id',
        'dre_id', 'escola_id',
        'data_inicio', 'data_fim',
        'situacao', 'prioridade',
        'pf_planejado', 'pf_entregue',
        'ust_planejada', 'ust_entregue',
        'horas_planejadas', 'horas_registradas',
        'status',
    ];

    // Relações principais
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itemContrato()
    {
        return $this->belongsTo(ContratoItem::class, 'itemcontrato_id');
    }

    public function gerenteTecnico()
    {
        return $this->belongsTo(User::class, 'gerente_tecnico_id');
    }

    public function gerenteAdm()
    {
        return $this->belongsTo(User::class, 'gerente_adm_id');
    }

    public function dre()
    {
        return $this->belongsTo(DRE::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Relacionamentos com módulos auxiliares
    public function atividades()
    {
        return $this->hasMany(AtividadeTecnica::class);
    }

    public function apfs()
    {
        return $this->hasMany(Apf::class);
    }

    public function itens()
    {
        return $this->hasMany(ProjetoItem::class);
    }

    public function boletins()
    {
        return $this->hasMany(BoletimMedicao::class);
    }

    public function processoInstancia()
    {
        return $this->morphOne(\App\Models\ProcessoInstancia::class, 'referencia');
    }
}
