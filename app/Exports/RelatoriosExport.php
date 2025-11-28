<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RelatoriosExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $relatorios;

    public function __construct(Collection $relatorios)
    {
        $this->relatorios = $relatorios;
    }

    public function collection()
    {
        return $this->relatorios;
    }

    public function headings(): array
    {
        return ['ID', 'Título', 'Tipo', 'Usuário', 'Data'];
    }

    public function map($relatorio): array
    {
        return [
            $relatorio->id,
            $relatorio->titulo,
            $relatorio->tipo,
            optional($relatorio->user)->name,
            optional($relatorio->created_at)->format('d/m/Y H:i'),
        ];
    }
}
