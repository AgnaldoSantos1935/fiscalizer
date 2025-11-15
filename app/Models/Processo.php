<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    protected $fillable = [
        'nome', 'codigo', 'descricao', 'versao', 'ativo',
    ];

    public function etapas()
    {
        return $this->hasMany(ProcessoEtapa::class)->orderBy('ordem');
    }

    public function fluxos()
    {
        return $this->hasMany(ProcessoFluxo::class);
    }

    public function instancias()
    {
        return $this->hasMany(ProcessoInstancia::class);
    }
}
