<?php

namespace App\Http\Controllers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'data_pagamento' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'arquivo_comprovante_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'observacao' => 'nullable|string|max:255',
        ];
    }
}
