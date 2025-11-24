<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleActionsSeeder extends Seeder
{
    public function run(): void
    {
        $actionsByCode = Action::all()->keyBy('codigo');

        $grant = function (Role $role, array $codes) use ($actionsByCode) {
            $ids = collect($codes)
                ->map(fn ($c) => $actionsByCode[$c]->id ?? null)
                ->filter()
                ->values()
                ->all();
            if (empty($ids)) {
                return;
            }
            $role->actions()->syncWithoutDetaching($ids);
        };

        // Admin e Administrador: tudo (via wildcard groups)
        foreach (['Administrador', 'admin'] as $adminName) {
            if ($admin = Role::where('nome', $adminName)->first()) {
                $grant($admin, [
                    'system.admin',
                    'contratos.*', 'medicoes.*', 'financeiro.*', 'documentos.*', 'ocorrencias.*', 'apf.*', 'usuarios.*',
                    // Receber todos eventos de notificações
                    'notificacoes.*',
                ]);
            }
        }

        // Gestor
        foreach (['gestor', 'Gestor de Contrato'] as $gestorName) {
            if ($gestor = Role::where('nome', $gestorName)->first()) {
                $grant($gestor, [
                    'contratos_view', 'contratos_edit',
                    'medicoes_homologar',
                    'financeiro_pagamentos_view', 'financeiro_pagamentos_autorizar',
                    'documentos_assinar', 'dashboard_ver_gestor',
                    // Notificações (granular)
                    'notificacoes.contratos.contrato_criado',
                    'notificacoes.contratos.contrato_atualizado',
                    'notificacoes.projetos.projeto_criado',
                    'notificacoes.projetos.projeto_atualizado',
                    'notificacoes.projetos.pf_calculado',
                    'notificacoes.projetos.ust_calculada',
                ]);
            }
        }

        // Fiscal técnico
        if ($fiscalTec = Role::where('nome', 'fiscal_tecnico')->first()) {
            $grant($fiscalTec, [
                'contratos_view',
                'medicoes_validar', 'medicoes_contestar',
                'ocorrencias_registrar',
                'documentos_assinar',
                'apf_calcular',
                // Notificações (granular)
                'notificacoes.medicoes.medicao_criada',
                'notificacoes.projetos.requisito_criado',
                'notificacoes.projetos.pf_calculado',
                'notificacoes.projetos.ust_calculada',
            ]);
        }

        // Fiscal administrativo
        if ($fiscalAdm = Role::where('nome', 'fiscal_administrativo')->first()) {
            $grant($fiscalAdm, [
                'contratos_view', 'contratos_create', 'contratos_edit',
                'financeiro_pagamentos_view',
                'financeiro.registrar_empenho',
                'financeiro.registrar_pagamento',
                'documentos_assinar',
                // Notificações (granular)
                'notificacoes.contratos.documento_anexado',
                'notificacoes.contratos.contrato_atualizado',
                'notificacoes.projetos.projeto_atualizado',
            ]);
        }
    }
}
