<?php

return [
    'events' => [
        // Workflow agregado de medição
        'notificacoes.medicoes.workflow_medicao' => [
            'title' => 'Workflow da Medição',
            'message' => 'Controle das etapas da medição com responsáveis e prioridade.',
            'channels' => ['database'],
            'enabled' => true,
            'priority' => 'normal',
            'recipient_scope' => 'rbac',
            'should_generate' => false,
            'workflow' => [
                [
                    'step' => 1,
                    'action' => 'medicoes.criar',
                    'responsible' => 'Executor',
                    'notify' => true,
                    'priority' => 'normal',
                ],
                [
                    'step' => 2,
                    'action' => 'medicoes.validar_pf',
                    'responsible' => 'Fiscal Técnico',
                    'notify' => true,
                    'priority' => 'high',
                ],
                [
                    'step' => 3,
                    'action' => 'medicoes.homologar',
                    'responsible' => 'Gestor',
                    'notify' => true,
                    'priority' => 'high',
                ],
                [
                    'step' => 4,
                    'action' => 'financeiro.autorizar',
                    'responsible' => 'Administrativo',
                    'notify' => true,
                    'priority' => 'critical',
                ],
            ],
        ],

        // Eventos por etapa (úteis para notificações individuais)
        'notificacoes.medicoes.criar' => [
            'title' => 'Medição criada',
            'message' => 'Medição {medicao.id} criada no projeto {projeto.id}.',
            'channels' => ['database'],
            'enabled' => true,
            'priority' => 'normal',
            'recipient_scope' => 'rbac',
        ],
        'notificacoes.medicoes.validar_pf' => [
            'title' => 'Medição enviada para validação de PF',
            'message' => 'Medição {medicao.id} aguardando validação de PF.',
            'channels' => ['database'],
            'enabled' => true,
            'priority' => 'high',
            'recipient_scope' => 'rbac',
        ],
        'notificacoes.medicoes.homologar' => [
            'title' => 'Medição aguardando homologação',
            'message' => 'Medição {medicao.id} aguardando homologação do gestor.',
            'channels' => ['database'],
            'enabled' => true,
            'priority' => 'high',
            'recipient_scope' => 'rbac',
        ],
        'notificacoes.financeiro.autorizar' => [
            'title' => 'Autorização financeira requerida',
            'message' => 'Medição {medicao.id} aguardando autorização do financeiro.',
            'channels' => ['database'],
            'enabled' => true,
            'priority' => 'critical',
            'recipient_scope' => 'rbac',
        ],
    ],
];
