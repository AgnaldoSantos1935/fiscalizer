<?php

namespace Database\Seeders;

use App\Models\ProjetoSoftware;
use Illuminate\Database\Seeder;

class ProjetoSoftwareSeeder extends Seeder
{
    public function run(): void
    {
        ProjetoSoftware::firstOrCreate([
            'codigo' => 'APF0132',
        ], [
            'titulo' => 'Planejamento de Matrícula — Dashboards',
            'sistema' => 'SIGEP',
            'modulo' => 'Planejamento de Matrícula',
            'submodulo' => 'Dashboards',
            'solicitante' => 'CEMEC / DPLAN / SEDUC',
            'fornecedor' => 'Montreal Informática S.A.',
            'pontos_funcao' => 114.40,
            'data_solicitacao' => '2025-10-28',
            'data_homologacao' => '2025-11-10',
            'situacao' => 'Homologado',
        ]);
    }
}
