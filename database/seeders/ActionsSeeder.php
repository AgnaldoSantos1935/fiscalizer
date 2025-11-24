<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Administração do Sistema
            ['codigo' => 'system.admin', 'nome' => 'Administrador do Sistema', 'descricao' => 'Controle total de configurações e fluxos', 'modulo' => 'usuarios'],
            // Contratos
            ['codigo' => 'contratos_view', 'nome' => 'Visualizar Contratos', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos_create', 'nome' => 'Criar Contratos', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos_edit', 'nome' => 'Editar Contratos', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos_delete', 'nome' => 'Excluir Contratos', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos_gestor_atribuir', 'nome' => 'Atribuir Gestor de Contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.*', 'nome' => 'Grupo: Contratos', 'descricao' => 'Permissões de contratos', 'modulo' => 'contratos'],

            // Contratos (dot-notation por funcionalidade)
            ['codigo' => 'contratos.view', 'nome' => 'Visualizar contratos', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.create', 'nome' => 'Criar contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.edit', 'nome' => 'Editar contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.delete', 'nome' => 'Excluir contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.anexar_documento', 'nome' => 'Anexar documento ao contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.gerar_os', 'nome' => 'Gerar Ordem de Serviço', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.enviar_email', 'nome' => 'Enviar e-mail do contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.visualizar_cronograma', 'nome' => 'Visualizar cronograma', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.renovar', 'nome' => 'Renovar contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.aditar', 'nome' => 'Aditar contrato', 'descricao' => null, 'modulo' => 'contratos'],
            ['codigo' => 'contratos.reajustar', 'nome' => 'Reajustar contrato', 'descricao' => null, 'modulo' => 'contratos'],

            // Medições
            ['codigo' => 'medicoes_criar', 'nome' => 'Criar Medições', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes_validar', 'nome' => 'Validar Medições', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes_contestar', 'nome' => 'Contestar Medições', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes_homologar', 'nome' => 'Homologar Medições', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.*', 'nome' => 'Grupo: Medições', 'descricao' => 'Permissões de medições', 'modulo' => 'medicoes'],

            // Medições (dot-notation por funcionalidade)
            ['codigo' => 'medicoes.criar', 'nome' => 'Criar medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.editar', 'nome' => 'Editar medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.excluir', 'nome' => 'Excluir medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.validar_pf', 'nome' => 'Validar PF da medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.validar_ust', 'nome' => 'Validar UST da medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.enviar_gestor', 'nome' => 'Enviar medição ao gestor', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.homologar', 'nome' => 'Homologar medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.contestar', 'nome' => 'Contestar medição', 'descricao' => null, 'modulo' => 'medicoes'],
            ['codigo' => 'medicoes.publicar_relatorio', 'nome' => 'Publicar relatório da medição', 'descricao' => null, 'modulo' => 'medicoes'],

            // Financeiro
            ['codigo' => 'financeiro_pagamentos_view', 'nome' => 'Visualizar Pagamentos', 'descricao' => null, 'modulo' => 'financeiro'],
            ['codigo' => 'financeiro_pagamentos_autorizar', 'nome' => 'Autorizar Pagamentos', 'descricao' => null, 'modulo' => 'financeiro'],
            ['codigo' => 'financeiro.registrar_empenho', 'nome' => 'Registrar empenho', 'descricao' => null, 'modulo' => 'financeiro'],
            ['codigo' => 'financeiro.registrar_pagamento', 'nome' => 'Registrar pagamento', 'descricao' => null, 'modulo' => 'financeiro'],
            ['codigo' => 'financeiro.*', 'nome' => 'Grupo: Financeiro', 'descricao' => 'Permissões do financeiro', 'modulo' => 'financeiro'],

            // Documentos
            ['codigo' => 'documentos_view', 'nome' => 'Visualizar Documentos', 'descricao' => null, 'modulo' => 'documentos'],
            ['codigo' => 'documentos_assinar', 'nome' => 'Assinar Documentos', 'descricao' => null, 'modulo' => 'documentos'],
            ['codigo' => 'documentos_anexar', 'nome' => 'Anexar Documentos', 'descricao' => null, 'modulo' => 'documentos'],
            ['codigo' => 'documentos.*', 'nome' => 'Grupo: Documentos', 'descricao' => 'Permissões de documentos', 'modulo' => 'documentos'],

            // Ocorrências
            ['codigo' => 'ocorrencias_view', 'nome' => 'Visualizar Ocorrências', 'descricao' => null, 'modulo' => 'ocorrencias'],
            ['codigo' => 'ocorrencias_registrar', 'nome' => 'Registrar Ocorrências', 'descricao' => null, 'modulo' => 'ocorrencias'],
            ['codigo' => 'ocorrencias.*', 'nome' => 'Grupo: Ocorrências', 'descricao' => 'Permissões de ocorrências', 'modulo' => 'ocorrencias'],

            // APF
            ['codigo' => 'apf_calcular', 'nome' => 'Calcular APF', 'descricao' => null, 'modulo' => 'apf'],
            ['codigo' => 'apf.*', 'nome' => 'Grupo: APF', 'descricao' => 'Permissões de APF', 'modulo' => 'apf'],

            // Projetos (dot-notation por funcionalidade)
            ['codigo' => 'projetos.criar', 'nome' => 'Criar projeto', 'descricao' => null, 'modulo' => 'projetos'],
            ['codigo' => 'projetos.editar_requisitos', 'nome' => 'Editar requisitos do projeto', 'descricao' => null, 'modulo' => 'projetos'],
            ['codigo' => 'projetos.criar_funcao_sistema', 'nome' => 'Criar função de sistema', 'descricao' => null, 'modulo' => 'projetos'],
            ['codigo' => 'projetos.calcular_pf', 'nome' => 'Calcular PF do projeto', 'descricao' => null, 'modulo' => 'projetos'],
            ['codigo' => 'projetos.calcular_ust', 'nome' => 'Calcular UST do projeto', 'descricao' => null, 'modulo' => 'projetos'],
            ['codigo' => 'projetos.aprovar_escopo', 'nome' => 'Aprovar escopo do projeto', 'descricao' => null, 'modulo' => 'projetos'],

            // Dashboard
            ['codigo' => 'dashboard_ver_gestor', 'nome' => 'Ver Dashboard do Gestor', 'descricao' => null, 'modulo' => 'dashboard'],
            ['codigo' => 'rbac_gerenciar', 'nome' => 'Gerenciar RBAC (Roles × Actions)', 'descricao' => 'Acesso à tela de gestão de permissões', 'modulo' => 'usuarios'],

            // Usuários
            ['codigo' => 'usuarios.*', 'nome' => 'Grupo: Usuários', 'descricao' => 'Permissões relacionadas a usuários', 'modulo' => 'usuarios'],

            // Notificações: gestão e eventos (gerenciadas via RBAC)
            ['codigo' => 'notificacoes.*', 'nome' => 'Grupo: Notificações', 'descricao' => 'Permissões de notificações e eventos', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.gerenciar', 'nome' => 'Gerenciar notificações e assinaturas', 'descricao' => 'Permite configurar assinaturas de eventos', 'modulo' => 'notificacoes'],

            // Eventos: Contratos
            ['codigo' => 'notificacoes.contratos.contrato_criado', 'nome' => 'Evento: Contrato criado', 'descricao' => 'Disparado ao criar contrato', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.contratos.contrato_atualizado', 'nome' => 'Evento: Contrato atualizado', 'descricao' => 'Disparado ao atualizar contrato', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.contratos.contrato_em_atraso', 'nome' => 'Evento: Contrato em atraso', 'descricao' => 'Detectado atraso em entregas/vigência', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.contratos.contrato_proximo_vencimento', 'nome' => 'Evento: Contrato próximo do vencimento', 'descricao' => 'Alerta de vencimento iminente', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.contratos.termo_aditivo_solicitado', 'nome' => 'Evento: Termo aditivo solicitado', 'descricao' => 'Solicitação de aditivo registrada', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.contratos.documento_anexado', 'nome' => 'Evento: Documento anexado ao contrato', 'descricao' => 'Novo documento vinculado ao contrato', 'modulo' => 'notificacoes'],

            // Eventos: Medições / APF / UST
            ['codigo' => 'notificacoes.medicoes.medicao_criada', 'nome' => 'Evento: Medição criada', 'descricao' => 'Nova medição registrada', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_enviada_fiscal', 'nome' => 'Evento: Medição enviada ao fiscal', 'descricao' => 'Fluxo enviou medição ao fiscal', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_validada_tecnico', 'nome' => 'Evento: Medição validada pelo técnico', 'descricao' => 'Validação técnica concluída', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_contestada', 'nome' => 'Evento: Medição contestada', 'descricao' => 'Contestação registrada na medição', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_homologada_gestor', 'nome' => 'Evento: Medição homologada pelo gestor', 'descricao' => 'Homologação concluída pelo gestor', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_enviada_financeiro', 'nome' => 'Evento: Medição enviada ao financeiro', 'descricao' => 'Fluxo enviou medição ao financeiro', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_rejeitada', 'nome' => 'Evento: Medição rejeitada', 'descricao' => 'Rejeição registrada pelo responsável', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.medicoes.medicao_pendente_analise', 'nome' => 'Evento: Medição pendente de análise', 'descricao' => 'Aguardando análise em alguma etapa', 'modulo' => 'notificacoes'],

            // Eventos: Projetos
            ['codigo' => 'notificacoes.projetos.requisito_criado', 'nome' => 'Evento: Requisito criado', 'descricao' => 'Novo requisito adicionado ao projeto', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.projetos.escopo_validado', 'nome' => 'Evento: Escopo validado', 'descricao' => 'Validação de escopo concluída', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.projetos.pf_calculado', 'nome' => 'Evento: PF calculado', 'descricao' => 'Pontos de função calculados', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.projetos.ust_calculada', 'nome' => 'Evento: UST calculada', 'descricao' => 'Unidades de serviço técnico calculadas', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.projetos.funcoes_aprovadas', 'nome' => 'Evento: Funções aprovadas', 'descricao' => 'Funções de sistema aprovadas', 'modulo' => 'notificacoes'],

            // Eventos: Monitoramento
            ['codigo' => 'notificacoes.monitoramento.host_offline', 'nome' => 'Evento: Host offline', 'descricao' => 'Host detectado offline', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.monitoramento.host_online', 'nome' => 'Evento: Host online', 'descricao' => 'Host voltou a ficar online', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.monitoramento.sla_quebra', 'nome' => 'Evento: Quebra de SLA', 'descricao' => 'SLA não atingido', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.monitoramento.latencia_alta', 'nome' => 'Evento: Latência alta', 'descricao' => 'Latência acima do limiar configurado', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.monitoramento.falha_repetida', 'nome' => 'Evento: Falha repetida', 'descricao' => 'Falhas recorrentes detectadas', 'modulo' => 'notificacoes'],

            // Eventos: Fiscalização
            ['codigo' => 'notificacoes.fiscalizacao.ocorrencia_registrada', 'nome' => 'Evento: Ocorrência registrada', 'descricao' => 'Nova ocorrência registrada', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.fiscalizacao.fiscalizacao_em_andamento', 'nome' => 'Evento: Fiscalização em andamento', 'descricao' => 'Fiscalização iniciada', 'modulo' => 'notificacoes'],
            ['codigo' => 'notificacoes.fiscalizacao.fiscalizacao_finalizada', 'nome' => 'Evento: Fiscalização finalizada', 'descricao' => 'Fiscalização concluída', 'modulo' => 'notificacoes'],
        ];

        foreach ($items as $item) {
            Action::updateOrCreate(
                ['codigo' => $item['codigo']],
                [
                    'nome' => $item['nome'],
                    'descricao' => $item['descricao'],
                    'modulo' => $item['modulo'],
                ]
            );
        }
    }
}
