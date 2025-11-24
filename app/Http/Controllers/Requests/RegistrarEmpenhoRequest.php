<?php

namespace App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarEmpenhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'numero' => 'required|string|max:30|unique:empenhos,numero',
            'empresa_id' => 'nullable|exists:empresas,id',
            'processo' => 'nullable|string|max:50',
            'data_empenho' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'pdf_oficial' => 'nullable|file|mimes:pdf|max:20480',
        ];
    }
}
