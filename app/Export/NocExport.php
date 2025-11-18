<?php

namespace App\Exports;

use App\Models\Host;
use Maatwebsite\Excel\Concerns\FromCollection;

class NocExport implements FromCollection
{
    public function collection()
    {
        return Host::select('id', 'nome_conexao', 'host_alvo', 'status')->get();
    }
}
