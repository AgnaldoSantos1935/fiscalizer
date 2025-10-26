<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcorrenciaFiscalizacao extends Model
{
    use HasFactory;

    // 👇 Corrige o nome da tabela (evita erro de pluralização)
    protected $table = 'ocorrencias_fiscalizacao';

    /**
     * Campos que podem ser preenchidos em massa (fillable)
     */
    protected $fillable = [
        'fiscalizacao_id',
        'tipo',
        'descricao',
        'data_ocorrencia',
        'status',
        'responsavel',
        'anexo',
    ];

    /**
     * Tipos de status possíveis
     * (pode ser útil em select/dropdown)
     */
    public const STATUS = [
        'pendente' => 'Pendente',
        'em_analise' => 'Em Análise',
        'resolvido' => 'Resolvido',
    ];

    /**
     * Relacionamentos
     */

    // 👇 Cada ocorrência pertence a uma fiscalização
    public function fiscalizacao()
    {
        return $this->belongsTo(Fiscalizacao::class);
    }

    // 👇 Caso exista vínculo com medição
    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    // 👇 Caso o fiscal seja um usuário autenticado
    public function usuario()
    {
        return $this->belongsTo(User::class, 'responsavel', 'id');
    }

    /**
     * Acessores / Mutators (opcionais)
     */

    // Retorna data formatada (pt-BR)
    public function getDataOcorrenciaFormatadaAttribute()
    {
        return \Carbon\Carbon::parse($this->data_ocorrencia)->format('d/m/Y');
    }

    // Caminho completo do anexo (caso haja upload de arquivo)
    public function getAnexoUrlAttribute()
    {
        return $this->anexo ? asset('storage/ocorrencias/' . $this->anexo) : null;
    }
}
