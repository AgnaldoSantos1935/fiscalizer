<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjetoSoftwareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:30|unique:projetos_software,codigo',
            'titulo' => 'required|string|max:255',
            'sistema' => 'nullable|string|max:120',
            'modulo' => 'nullable|string|max:120',
            'submodulo' => 'nullable|string|max:120',
            'solicitante' => 'nullable|string|max:255',
            'fornecedor' => 'nullable|string|max:255',
            'pontos_funcao' => 'nullable|numeric',
            'data_solicitacao' => 'nullable|date',
            'data_homologacao' => 'nullable|date',
            'situacao' => 'nullable|in:Analise,Em Execucao,Homologado,Pago,Suspenso',
            'valor_estimado' => 'nullable|numeric',
            'contrato_id' => 'nullable|exists:contratos,id',
        ];
    }
}
