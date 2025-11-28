<?php

use App\Http\Controllers\AntifraudeDashboardController;
use App\Http\Controllers\ApfController;
use App\Http\Controllers\Api\ProjetoApiController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\BoletimMedicaoController;
use App\Http\Controllers\ContratoConformidadeController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\ContratoInteligenteController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoProjetoController;
use App\Http\Controllers\DREController;
use App\Http\Controllers\EmpenhoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\EscolaController;
use App\Http\Controllers\FiscalizacaoProjetoController;
use App\Http\Controllers\FluxoOrdemServicoController;
use App\Http\Controllers\FuncaoSistemaController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\HostDashboardController;
use App\Http\Controllers\HostMonitorController;
use App\Http\Controllers\HostTesteController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\MedicaoDocumentoController;
use App\Http\Controllers\MedicaoTelcoController;
use App\Http\Controllers\MonitoramentoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OcorrenciaController;
use App\Http\Controllers\OcorrenciaFiscalizacaoController;
use App\Http\Controllers\PagamentosController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\ProjetoRelacionamentoController;
use App\Http\Controllers\ProjetoWorkflowController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\SituacaoContratoController;
use App\Http\Controllers\TermoReferenciaController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser as PdfParser;
use App\Http\Controllers\InventarioController as InvController;
use App\Http\Controllers\TelemetriaDashboardController;

// Página inicial (redireciona para login ou dashboard)

Route::get('/', function () {
    return view('site.index');
});
Route::get('site/imagens', function () {
    return view('site.imagens');
})->name('site.imagens');
// Contato público (envio de e-mail)
Route::post('site/contato/enviar', function (Request $request) {
    $data = $request->validate([
        'nome' => 'required|string|max:150',
        'email' => 'required|email|max:255',
        'mensagem' => 'required|string|max:5000',
    ]);

    $to = config('mail.from.address');
    $fromName = config('mail.from.name');

    Mail::raw(
        "Mensagem de contato\n\nNome: {$data['nome']}\nEmail: {$data['email']}\n\n{$data['mensagem']}",
        function ($m) use ($to, $fromName) {
            $m->to($to)->subject('Contato — Fiscalizer');
            if ($fromName && $to) {
                $m->from($to, $fromName);
            }
        }
    );

    return back()->with('success', 'Mensagem enviada com sucesso!');
})->name('site.contato.enviar');

// Chatbot público (respostas básicas)
Route::post('site/chatbot/ask', function (Request $request) {
    $q = strtolower(trim($request->input('pergunta', '')));
    $sources = $request->input('sources');
    $hist = $request->session()->get('chat_hist', []);
    $kb = $request->session()->get('chat_kb', []);
    $chunks = $request->session()->get('chat_kb_chunks', []);
    $embUrl = env('EMBEDDINGS_URL');
    $reset = (bool) $request->input('reset', false);

    if ($reset) {
        $hist = [];
        $request->session()->put('chat_hist', []);
        return response()->json(['resposta' => null, 'hist' => [], 'ingest_count' => count($kb), 'chunks' => count($chunks), 'sugestoes' => [], 'hist_preview' => []]);
    }

    // ingestão simples de fontes públicas (HTML/texto e PDF)
    if (is_array($sources)) {
        foreach ($sources as $url) {
            $u = trim((string) $url);
            if (! $u || ! preg_match('#^https?://#i', $u)) { continue; }
            try {
                $resp = Http::timeout(10)->get($u);
                if ($resp->ok()) {
                    $ct = strtolower($resp->header('Content-Type', 'application/octet-stream'));
                    $raw = $resp->body();
                    $text = $raw;
                    if (str_contains($ct, 'pdf') || preg_match('/\.pdf(\?|$)/i', $u)) {
                        try {
                            $parser = new PdfParser();
                            $pdf = $parser->parseContent($raw);
                            $text = $pdf->getText();
                        } catch (\Throwable $e2) {
                            $text = 'Falha ao extrair texto do PDF. Fonte: ' . $u;
                        }
                    } elseif (str_contains($ct, 'html')) {
                        $text = strip_tags($raw);
                    }
                    $kb[$u] = [
                        'title' => $u,
                        'url' => $u,
                        'text' => mb_substr($text ?? '', 0, 50000),
                    ];
                    $plain = (string) $kb[$u]['text'];
                    $len = mb_strlen($plain);
                    $size = 1500;
                    for ($i = 0; $i < $len; $i += $size) {
                        $chunkText = mb_substr($plain, $i, $size);
                        $terms = collect(preg_split('/\W+/u', mb_strtolower($chunkText)))->filter(fn($t) => mb_strlen($t) > 2)->values()->all();
                        $vec = null;
                        if ($embUrl) {
                            try {
                                $er = Http::timeout(8)->post($embUrl, ['text' => $chunkText]);
                                if ($er->ok()) { $j = $er->json(); if (is_array($j) && isset($j['vector']) && is_array($j['vector'])) { $vec = $j['vector']; } }
                            } catch (\Throwable $e3) { }
                        }
                        $chunks[] = [
                            'url' => $u,
                            'text' => $chunkText,
                            'terms' => $terms,
                            'vec' => $vec,
                        ];
                    }
                }
            } catch (\Throwable $e) { }
        }
        $request->session()->put('chat_kb', $kb);
        $request->session()->put('chat_kb_chunks', $chunks);
    }
    $base = [
        'lei14133' => 'A Lei 14.133/2021 estrutura fases e prazos. O Fiscalizer controla etapas, prazos automáticos, registros e relatórios de conformidade.',
        'lgpd' => 'Aplicamos RBAC, registro de acessos e mínimos necessários. Fluxos e evidências respeitam a LGPD (Lei 13.709).',
        'lai' => 'Portal de integridade com dados públicos, trilhas e relatórios exportáveis apoiam o cumprimento da LAI.',
        'govdigital' => 'Compatível com o Marco do Governo Digital (Lei 14.129): APIs, assinaturas eletrônicas e integração gov.br.',
        'medicoes' => 'Medições e SLAs com evidências anexas, validações e notificações garantem execução e conformidade.',
        'financeiro' => 'Empenhos e pagamentos com histórico e documentos vinculados para prestação de contas e auditoria.',
        'planos' => 'Planos Essencial, Profissional e Governo. Integram conformidade, BI e integrações, conforme necessidade.',
        'sobre' => 'Fiscalizer é uma plataforma integrada de governança de contratos com foco em transparência e conformidade.',
        'contato' => 'Use o formulário de contato na página ou o chat para dúvidas rápidas.',
    ];

    $res = 'Sou o assistente do Fiscalizer. Pergunte sobre legislação, módulos, medições, prazos, planos ou contato.';
    $topic = null;
    if ($q) {
        $greet = ['oi','olá','ola','bom dia','boa tarde','boa noite','hey','e aí','eaí'];
        $thanks = ['obrigado','obrigada','valeu','agradecido','grato'];
        $bye = ['tchau','até logo','até mais','flw','falou'];
        if (in_array(trim($q), $greet)) { $res = 'Olá! Posso ajudar com dúvidas sobre contratos, medições, legislação e integrações.'; $topic = 'smalltalk'; }
        elseif (str_contains($q, 'quem é você') || str_contains($q, 'quem é vc')) { $res = 'Sou o assistente do Fiscalizer, focado em orientar sobre governança de contratos e processos relacionados.'; $topic = 'smalltalk'; }
        elseif (collect($thanks)->first(fn($t) => str_contains($q, $t))) { $res = 'De nada! Se precisar, posso explicar módulos, prazos ou buscar fundamentos.'; $topic = 'smalltalk'; }
        elseif (collect($bye)->first(fn($t) => str_contains($q, $t))) { $res = 'Até mais! Quando quiser, retorno com orientações e fontes.'; $topic = 'smalltalk'; }
        elseif (str_contains($q, '14.133') || str_contains($q, 'nova lei') || str_contains($q, 'licit')) { $res = $base['lei14133']; $topic = 'lei14133'; }
        elseif (str_contains($q, 'lgpd') || str_contains($q, 'privacidade')) { $res = $base['lgpd']; $topic = 'lgpd'; }
        elseif (str_contains($q, 'lai') || str_contains($q, 'acesso') || str_contains($q, 'transpar')) { $res = $base['lai']; $topic = 'lai'; }
        elseif (str_contains($q, 'governo digital') || str_contains($q, '14.129') || str_contains($q, 'api') || str_contains($q, 'gov.br')) { $res = $base['govdigital']; $topic = 'govdigital'; }
        elseif (str_contains($q, 'medi') || str_contains($q, 'sla') || str_contains($q, 'ordem de serviço') || str_contains($q, 'os')) { $res = $base['medicoes']; $topic = 'medicoes'; }
        elseif (str_contains($q, 'pagamento') || str_contains($q, 'empenho')) { $res = $base['financeiro']; $topic = 'financeiro'; }
        elseif (str_contains($q, 'plano') || str_contains($q, 'assinatura') || str_contains($q, 'contratar')) { $res = $base['planos']; $topic = 'planos'; }
        elseif (str_contains($q, 'sobre') || str_contains($q, 'o que') || str_contains($q, 'utilidade')) { $res = $base['sobre']; $topic = 'sobre'; }
        elseif (str_contains($q, 'contato') || str_contains($q, 'suporte')) { $res = $base['contato']; $topic = 'contato'; }
        else {
            $qTerms = collect(preg_split('/\W+/u', $q))->filter(fn($t) => mb_strlen($t) > 2)->map(fn($t) => mb_strtolower($t))->values()->all();
            $useEmb = $embUrl ? true : false;
            $qVec = null;
            if ($useEmb) {
                try {
                    $qr = Http::timeout(8)->post($embUrl, ['text' => $q]);
                    if ($qr->ok()) { $jq = $qr->json(); if (is_array($jq) && isset($jq['vector']) && is_array($jq['vector'])) { $qVec = $jq['vector']; } }
                } catch (\Throwable $e4) { $useEmb = false; }
            }
            $best = null; $bestScore = -1;
            if ($useEmb && $qVec) {
                foreach ($chunks as $ch) {
                    $v = $ch['vec'] ?? null; if (!is_array($v)) continue;
                    $dot = 0; $nq = 0; $nv = 0;
                    foreach ($qVec as $k => $x) { $y = $v[$k] ?? 0; $dot += $x * $y; $nq += $x * $x; $nv += $y * $y; }
                    $sim = ($nq > 0 && $nv > 0) ? ($dot / (sqrt($nq) * sqrt($nv))) : 0;
                    if ($sim > $bestScore) { $bestScore = $sim; $best = $ch; }
                }
            } else {
                $N = count($chunks);
                $df = [];
                foreach ($chunks as $ch) {
                    $uniq = array_values(array_unique($ch['terms'] ?? []));
                    foreach ($uniq as $t) { $df[$t] = ($df[$t] ?? 0) + 1; }
                }
                $qCount = [];
                foreach ($qTerms as $t) { $qCount[$t] = ($qCount[$t] ?? 0) + 1; }
                $qW = [];
                foreach ($qCount as $t => $c) { $idf = log((($N ?: 1) + 1) / (($df[$t] ?? 0) + 1)) + 1; $qW[$t] = $c * $idf; }
                foreach ($chunks as $ch) {
                    $cCount = [];
                    foreach ($ch['terms'] as $t) { $cCount[$t] = ($cCount[$t] ?? 0) + 1; }
                    $cW = [];
                    foreach ($cCount as $t => $c) { $idf = log((($N ?: 1) + 1) / (($df[$t] ?? 0) + 1)) + 1; $cW[$t] = $c * $idf; }
                    $dot = 0; $nq = 0; $nc = 0;
                    foreach ($qW as $t => $wq) { $wc = $cW[$t] ?? 0; $dot += $wq * $wc; $nq += $wq * $wq; }
                    foreach ($cW as $t => $wc) { $nc += $wc * $wc; }
                    $sim = ($nq > 0 && $nc > 0) ? ($dot / (sqrt($nq) * sqrt($nc))) : 0;
                    if ($sim > $bestScore) { $bestScore = $sim; $best = $ch; }
                }
            }
            if ($best) {
                $snippet = trim(mb_substr((string) $best['text'], 0, 500));
                $res = 'Claro! Encontrei um trecho relevante:' . "\n\n" . $snippet . "\n\nFonte: " . ((string) ($best['url'] ?? ''));
                $topic = 'rag';
            } else {
                $res = 'Certo, não localizei algo específico. Posso orientar sobre Lei 14.133, LGPD, LAI, governo digital, medições, pagamentos, planos e contato.';
                $topic = 'generic';
            }
        }
    }

    $rephraseUrl = env('REPHRASE_URL');
    if ($rephraseUrl) {
        try {
            $rr = Http::timeout(6)->post($rephraseUrl, ['text' => $res, 'tone' => 'amigavel', 'locale' => 'pt-BR']);
            if ($rr->ok()) {
                $jrr = $rr->json();
                if (is_array($jrr) && isset($jrr['text']) && is_string($jrr['text'])) { $res = $jrr['text']; }
            }
        } catch (\Throwable $e5) { }
    } else {
        if (!str_starts_with($res, 'Olá!') && !str_starts_with($res, 'Claro!') && !str_starts_with($res, 'Certo,')) {
            $res = 'Claro! ' . $res;
        }
    }

    $hist[] = ['q' => $q, 'a' => $res, 't' => now()->format('H:i:s')];
    $hist = array_slice($hist, -20);
    $request->session()->put('chat_hist', $hist);

    $sug = [];
    if ($topic === 'lei14133') {
        $sug = ['Quais são as fases da Lei 14.133?', 'Como o sistema controla prazos?', 'Como registrar evidências de conformidade?'];
    } elseif ($topic === 'lgpd') {
        $sug = ['Como funciona RBAC no sistema?', 'Que logs de acesso são gerados?', 'Como tratar consentimentos?'];
    } elseif ($topic === 'lai') {
        $sug = ['Que dados ficam públicos?', 'Como exportar relatórios?', 'Como funciona trilha de auditoria?'];
    } elseif ($topic === 'govdigital') {
        $sug = ['Integração com gov.br', 'Assinaturas eletrônicas', 'APIs disponíveis'];
    } elseif ($topic === 'medicoes') {
        $sug = ['Como validar uma medição?', 'Como configurar SLAs?', 'Como anexar evidências?'];
    } elseif ($topic === 'financeiro') {
        $sug = ['Como registrar empenhos?', 'Como conciliar pagamentos?', 'Como emitir relatórios financeiros?'];
    } elseif ($topic === 'planos') {
        $sug = ['Diferenças entre planos', 'Como contratar um plano?', 'Quais integrações incluídas?'];
    } elseif ($topic === 'sobre') {
        $sug = ['Quais módulos existem?', 'Como acessar o sistema?', 'Como funciona a conformidade?'];
    } elseif ($topic === 'contato') {
        $sug = ['Como falar com suporte?', 'Onde enviar feedback?', 'Quais canais oficiais?'];
    } else {
        $sug = ['Me fale dos módulos do sistema', 'Como funcionam as medições?', 'Como acessar e usar o sistema?'];
    }

    $preview = array_slice($hist, -3);

    return response()->json(['resposta' => $res, 'hist' => $hist, 'ingest_count' => count($kb), 'chunks' => count($chunks), 'sugestoes' => $sug, 'hist_preview' => $preview]);
})->name('site.chatbot.ask');
// Home visível a qualquer usuário autenticado
Route::get('home', [DashboardController::class, 'index'])->name('home');

// Atalho enxuto para abrir diretamente o perfil do usuário autenticado
Route::get('meu-perfil', [UserProfileController::class, 'me'])
    ->middleware('auth')
    ->name('user_profiles.me');

// Rotas de autenticação
Auth::routes(['register' => false, 'reset' => true]);

Route::middleware(['auth', 'password.expiration'])->group(function () {
    Route::resource('user_profiles', UserProfileController::class)
        ->middleware('can:view-index-user_profiles');
});

// 🔹 Rotas RESTful (CRUD completo)
Route::resource('escolas', EscolaController::class);
Route::resource('empresas', EmpresaController::class);
// Endpoint JSON para DataTables (Empresas)
Route::get('empresas/data', [EmpresaController::class, 'data'])->name('empresas.data');
// Verificação de CNPJ (AJAX/GET)
Route::get('empresas/verificar', [EmpresaController::class, 'verificar'])->name('empresas.verificar');
Route::resource('hosts', HostController::class);
Route::resource('contratos', ContratoController::class);
Route::resource('medicoes', MedicaoController::class);
// Endpoint JSON para DataTables (Medições)
Route::get('medicoes/data', [MedicaoController::class, 'data'])->name('medicoes.data');
Route::resource('funcoes-sistema', FuncaoSistemaController::class);
Route::resource('documentos', DocumentoController::class);
Route::get('documentos/data', [DocumentoController::class, 'data'])->name('documentos.data');
// Visualizador de PDFs e streaming inline
Route::get('documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
Route::get('documentos/{documento}/stream', [DocumentoController::class, 'stream'])->name('documentos.stream');
Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
Route::get('documentos/{documento}/print', [DocumentoController::class, 'print'])->name('documentos.print');

// Atalho para abrir PDF do contrato
Route::get('contratos/{contrato}/pdf', [ContratoController::class, 'pdf'])->name('contratos.pdf');
// Cadastro de documentos vinculados ao contrato
Route::get('contratos/{contrato}/documentos/create', [App\Http\Controllers\ContratoController::class, 'createDocumento'])
    ->middleware('can.action:contratos.anexar_documento')
    ->name('contratos.documentos.create');
Route::resource('ocorrencias-fiscalizacao', OcorrenciaFiscalizacaoController::class);
Route::resource('ocorrencias', OcorrenciaController::class);
Route::resource('projetos', ProjetoController::class);
// API para DataTables da tela de Projetos
Route::get('/api/projetos', [ProjetoController::class, 'getJsonProjetos'])->name('api.projetos');
Route::resource('user_profiles', UserProfileController::class)
    ->middleware('can:view-index-user_profiles');
Route::resource('usuarios', UserProfileController::class)
    ->middleware('can:view-index-user_profiles');

// RBAC: Actions CRUD
Route::resource('actions', App\Http\Controllers\ActionController::class);
// RBAC: Gestão de vínculo Role × Action
Route::get('rbac/roles-actions', [App\Http\Controllers\RoleActionController::class, 'index'])
    ->middleware('can.action:system.admin')
    ->name('rbac.roles_actions.index');
Route::post('rbac/roles-actions/{role}', [App\Http\Controllers\RoleActionController::class, 'update'])
    ->middleware('can.action:system.admin')
    ->name('rbac.roles_actions.update');
Route::resource('empresas', EmpresaController::class);
// Rota explícita para POST de criação de empenhos para evitar conflitos no ambiente de teste
Route::post('empenhos', [EmpenhoController::class, 'store'])->name('empenhos.store');
Route::resource('empenhos', EmpenhoController::class);
// PDF de Pretensão de Empenho (submissão ao gestor do contrato)
Route::get('empenhos/{id}/pretensao/pdf', [EmpenhoController::class, 'pretensaoPdf'])->name('empenhos.pretensao_pdf');
// Upload do PDF emitido (finaliza etapa Emitido)
Route::post('empenhos/{id}/emitido/upload', [EmpenhoController::class, 'uploadEmitidoPdf'])->name('empenhos.emitido_upload');
Route::post('empenhos/{id}/pretensao/solicitar', [EmpenhoController::class, 'solicitarPretensao'])->name('empenhos.pretensao_solicitar');
// Nova rota para salvar solicitação via formulário
Route::post('empenhos/{id}/solicitacao', [EmpenhoController::class, 'salvarSolicitacao'])->name('empenhos.solicitacao_salvar');
// Upload do comprovante de liquidação (finaliza etapa Pago)
Route::post('empenhos/{id}/pago/upload', [EmpenhoController::class, 'uploadComprovanteLiquidacao'])->name('empenhos.pago_upload');
// Registro de Empenho a partir de Pretensão (solicitacoes_empenho)
Route::get('financeiro/solicitacoes-empenho/{solicitacao}/registrar', [EmpenhoController::class, 'registrarFromSolicitacaoForm'])
    ->middleware(['auth', 'can.action:financeiro.registrar_empenho'])
    ->name('financeiro.solicitacoes.registrar_empenho.form');
Route::post('financeiro/solicitacoes-empenho/{solicitacao}/registrar', [EmpenhoController::class, 'registrarFromSolicitacaoStore'])
    ->middleware(['auth', 'can.action:financeiro.registrar_empenho'])
    ->name('financeiro.solicitacoes.registrar_empenho.store');
// Registro de Pagamento para um Empenho
Route::get('financeiro/empenhos/{empenho}/pagamentos/create', [PagamentosController::class, 'create'])
    ->middleware(['auth', 'can.action:financeiro.registrar_pagamento'])
    ->name('financeiro.pagamentos.create');
Route::post('financeiro/empenhos/{empenho}/pagamentos', [PagamentosController::class, 'store'])
    ->middleware(['auth', 'can.action:financeiro.registrar_pagamento'])
    ->name('financeiro.pagamentos.store');
// Aprovar solicitação e gerar PDF/Documento
Route::post('empenhos/solicitacoes/{solicitacao}/aprovar', [EmpenhoController::class, 'aprovarSolicitacao'])
    ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
    ->name('empenhos.solicitacoes.aprovar');
Route::resource('hosts', HostController::class);
Route::resource('dres', DREController::class);
Route::pattern('host_teste', '[0-9]+');
Route::resource('host_testes', HostTesteController::class)
    ->only(['index', 'show'])
    ->parameters(['host_testes' => 'host_teste'])
    ->whereNumber('host_teste');
Route::resource('situacoes', SituacaoContratoController::class);
Route::resource('projetos.apfs', ApfController::class); // nested: /projetos/{projeto}/apfs
Route::resource('projetos.fiscalizacoes', FiscalizacaoProjetoController::class)->shallow();
Route::resource('servidores', ServidorController::class);
Route::resource('boletins', BoletimMedicaoController::class);
Route::resource('demandas', DemandaController::class);

// Atas de Registro de Preços
Route::resource('atas', \App\Http\Controllers\AtaRegistroPrecoController::class);
Route::post('atas/{ata}/adesoes', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'storeAdesao'])
    ->name('atas.adesoes.store');
Route::post('adesoes/{adesao}/gerar-pdf', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'gerarAutorizacaoPdf'])
    ->name('adesoes.gerar_pdf');
Route::post('adesoes/{adesao}/status', [\App\Http\Controllers\AtaRegistroPrecoController::class, 'atualizarStatusAdesao'])
    ->name('adesoes.status');

// Módulo: Contratações
Route::prefix('contratacoes')->name('contratacoes.')->group(function () {
    Route::view('/', 'contratacoes.index')->name('index');
    Route::resource('termos-referencia', TermoReferenciaController::class)
        ->parameters(['termos-referencia' => 'tr']);
    Route::get('/api/termos-referencia', [TermoReferenciaController::class, 'getJson'])
        ->name('termos-referencia.api');
    Route::get('termos-referencia/{tr}/pdf', [TermoReferenciaController::class, 'pdf'])
        ->name('termos-referencia.pdf');
    Route::get('termos-referencia/{tr}/docx', [TermoReferenciaController::class, 'docx'])
        ->name('termos-referencia.docx');
    // Workflow simples de TR: enviar para aprovação e aprovar
    Route::post('termos-referencia/{tr}/enviar-aprovacao', [TermoReferenciaController::class, 'enviarAprovacao'])
        ->middleware(['auth'])
        ->name('termos-referencia.enviar-aprovacao');
    Route::post('termos-referencia/{tr}/aprovar', [TermoReferenciaController::class, 'aprovar'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.aprovar');
    Route::post('termos-referencia/{tr}/retornar-elaboracao', [TermoReferenciaController::class, 'retornarElaboracao'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.retornar-elaboracao');

    // Reprovar (voltar para rascunho com motivo)
    Route::post('termos-referencia/{tr}/reprovar', [TermoReferenciaController::class, 'reprovar'])
        ->middleware(['auth', 'role:Administrador,Gestor de Contrato'])
        ->name('termos-referencia.reprovar');

    // Itens do Termo de Referência (aninhados, com shallow destroy)
    Route::post('termos-referencia/{tr}/itens', [\App\Http\Controllers\TermoReferenciaItemController::class, 'store'])
        ->name('termos-referencia.itens.store');
    Route::put('termos-referencia/itens/{item}', [\App\Http\Controllers\TermoReferenciaItemController::class, 'update'])
        ->name('termos-referencia.itens.update');
    Route::delete('termos-referencia/itens/{item}', [\App\Http\Controllers\TermoReferenciaItemController::class, 'destroy'])
        ->name('termos-referencia.itens.destroy');
});

// 🔹 Rotas testes de rede
// 🔹 Rotas de testes de conexão (pings manuais, diagnóstico)
// Route::get('/teste-conexao', [App\Http\Controllers\HostController::class, 'index'])->name('teste_conexao.index');
// Route::post('/teste-conexao', [App\Http\Controllers\TesteConexaoController::class, 'testar'])->name('teste_conexao.testar');

// 🔹 Rotas de monitoramento automático (CRUD + histórico + teste)
// Route::resource('monitoramentos', MonitoramentoController::class)->except(['show']);
// Route::get('monitoramentos/{id}/testar', [MonitoramentoController::class, 'testar'])->name('monitoramentos.testar');
// Route::get('monitoramentos/{id}/historico', [MonitoramentoController::class, 'historico'])->name('monitoramentos.historico');

Route::get('empenho/{id}/imprimir', [EmpenhoController::class, 'imprimir'])
    ->name('empenho.imprimir');

// Ajuste para ambiente de teste onde a base URL inclui "/fiscalizer/public"
if (app()->environment('testing')) {
    Route::post('fiscalizer/public/empenhos', [EmpenhoController::class, 'store']);
}

// CADASTRO DE PERFIS DE USUÁRIOS
Route::get('user_profiles/index', [App\Http\Controllers\UserProfileController::class, 'index'])
    ->middleware('can:view-index-user_profiles')
    ->name('user_profiles.index');
Route::get('user_profiles/create', [App\Http\Controllers\UserProfileController::class, 'create'])
    ->middleware('can:view-create-user_profiles')
    ->name('user_profiles.create');
Route::get('user_profiles/show', [App\Http\Controllers\UserProfileController::class, 'show'])
    ->middleware('can:view-index-user_profiles')
    ->name('user_profiles.show');
// FIM DE USUÁRIOS

// FISCALIZAÇÃO PROJETO DE SOFTWARE
Route::post('fiscalizacoes/{fiscalizacao}/documentos', [DocumentoProjetoController::class, 'store'])->name('fiscalizacoes.documentos.store');
// FIM DE PROJETO DE SOFTWARE

// TESTES E MONITORAMENTO DE CONEXÕES

Route::get('/hosts/{id}', [HostController::class, 'show'])->name('api.hosts.show');
Route::get('/hosts.index', [HostController::class, 'index'])->name('hosts.index');
Route::get('/monitoramentos', [MonitoramentoController::class, 'index'])
    ->name('monitoramentos.index');
Route::get('/host_testes/dashboard', [HostDashboardController::class, 'index'])
    ->name('host_testes.dashboard');

// 🔹 Execução de teste manual (ping individual)
Route::post('/hosts/{id}/testar', [HostTesteController::class, 'executarTesteManual'])
    ->name('hosts.testar');

// Rotas para o mapa
Route::get('/mapas/escolas', [MapaController::class, 'index'])->name('mapas.escolas');
Route::get('medicoes/{medicao}/telco/mapa', [MedicaoTelcoController::class, 'mapa'])
    ->name('medicoes.telco.mapa');

Route::get('relatorios/gerar', [RelatorioController::class, 'gerar'])->name('relatorios.gerar');
Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
Route::post('relatorios', [RelatorioController::class, 'store'])->name('relatorios.store');
Route::get('relatorios/export/excel', [RelatorioController::class, 'exportExcel'])->name('relatorios.export.excel');
Route::get('relatorios/export/pdf', [RelatorioController::class, 'exportPdf'])->name('relatorios.export.pdf');
// BOLETIM DE MEDIÇÃO
Route::get('boletins/{id}/pdf', [BoletimMedicaoController::class, 'exportPdf'])->name('boletins.pdf');

// FIM DE BOLETIM DE MEDIÇÃO

Route::get('hotwire', function () {
    return view('hotwire.test');
})->name('hotwire.test');

Route::get('hotwire/partial', function (Request $request) {
    $count = (int) $request->query('count', 1);
    return response()->view('hotwire.partial', ['count' => $count]);
})->name('hotwire.partial');
// DASHBOARD PROJETOS
Route::get('/dashboard/projetos', [DashboardController::class, 'index'])->name('dashboard.projetos');
Route::get('dashboard/antifraude', [AntifraudeDashboardController::class, 'index'])
    ->name('dashboard.antifraude');
Route::get('/projetos/{projeto}/gantt', [ProjetoController::class, 'gantt'])
    ->name('projetos.gantt');
Route::get('/projetos/{projeto}/dashboard', [ProjetoController::class, 'dashboard'])
    ->name('projetos.dashboard');
Route::get('/projetos/{projeto}/relatorio/pdf', [ProjetoController::class, 'relatorioPdf'])
    ->name('projetos.relatorio.pdf');
Route::get('/projetos/index', [ProjetoController::class, 'index'])
    ->name('projetos.index');
Route::get('/projetos/create', [ProjetoController::class, 'create'])
    ->name('projetos.create');

// FINAL DE DASHBOARD PROJETOS
// DASHBOARD MONITORAMENTO DE CONEXÕES
Route::get('/monitoramentos', [MonitoramentoController::class, 'index'])
    ->name('monitoramentos.index');
Route::get('/monitoramentos/dashboard2', [MonitoramentoController::class, 'dashboard2'])
    ->name('monitoramentos.dashboard2');
Route::get('/monitoramentos/heatline', [MonitoramentoController::class, 'heatline'])
    ->name('monitoramentos.heatline');
Route::get('/monitoramentos/matrix', [MonitoramentoController::class, 'matrix'])
    ->name('monitoramentos.matrix');
// FINAL DE DASHBOARD MONITORAMENTO DE CONEXÕES
// INICIO DO NOC
// Rotas de exportação do NOC (somente se o controlador existir)
if (class_exists(\App\Http\Controllers\NocReportController::class)) {
    Route::get('/noc/export/pdf', [\App\Http\Controllers\NocReportController::class, 'pdf'])->name('noc.export.pdf');
    Route::get('/noc/export/excel', [\App\Http\Controllers\NocReportController::class, 'excel'])->name('noc.export.excel');
}

// FINAL DO NOC

// ROTAS DE PROJETOS
Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('requisitos', [ProjetoRelacionamentoController::class, 'requisitos']);
    Route::get('atividades', [ProjetoRelacionamentoController::class, 'atividades']);
    Route::get('cronograma', [ProjetoRelacionamentoController::class, 'cronograma']);
    Route::get('equipe', [ProjetoRelacionamentoController::class, 'equipe']);
});
// FINAL DA ROTA DE PROJETOS
// Rotas públicas para upload de documento técnico pela empresa
Route::get('empresa/upload/{token}', [FluxoOrdemServicoController::class, 'formUploadEmpresa'])
    ->name('empresa.upload_documento');
Route::post('empresa/upload/{token}', [FluxoOrdemServicoController::class, 'receberDocumentoEmpresa'])
    ->name('empresa.upload_documento_post');
// ROTAS DE REQUISITOS
Route::post('/requisitos', [RequisitoController::class, 'store'])->name('requisitos.store');
Route::post('/atividades', [AtividadeController::class, 'store'])->name('atividades.store');
Route::post('/cronograma', [CronogramaController::class, 'store'])->name('cronograma.store');
Route::post('/equipe', [EquipeController::class, 'store'])->name('equipe.store');

Route::put('/requisitos/{requisito}', [RequisitoController::class, 'update']);
Route::put('/atividades/{atividade}', [AtividadeController::class, 'update']);
Route::put('/cronograma/{cronograma}', [CronogramaController::class, 'update']);
Route::put('/equipe/{equipe}', [EquipeController::class, 'update']);

Route::delete('/requisitos/{requisito}', [RequisitoController::class, 'destroy']);
Route::delete('/atividades/{atividade}', [AtividadeController::class, 'destroy']);
Route::delete('/cronograma/{cronograma}', [CronogramaController::class, 'destroy']);
Route::delete('/equipe/{equipe}', [EquipeController::class, 'destroy']);
Route::get('/requisitos/{requisito}', [RequisitoController::class, 'show']);
Route::get('/atividades/{atividade}', [AtividadeController::class, 'show']);
Route::get('/cronograma/{cronograma}', [CronogramaController::class, 'show']);
Route::get('/equipe/{equipe}', [EquipeController::class, 'show']);
// FINAL DA ROTA DE REQUISITOS

// inicio das rotas de workflow BPM

Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('workflow', [ProjetoWorkflowController::class, 'show'])->name('projetos.workflow.show');
    Route::post('workflow/iniciar', [ProjetoWorkflowController::class, 'iniciar'])->name('projetos.workflow.iniciar');
    Route::post('workflow/avancar', [ProjetoWorkflowController::class, 'avancar'])->name('projetos.workflow.avancar');
});
Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('workflow', [ProjetoWorkflowController::class, 'show'])->name('projetos.workflow.show');
    Route::post('workflow/iniciar', [ProjetoWorkflowController::class, 'iniciar'])->name('projetos.workflow.iniciar');
    Route::post('workflow/avancar', [ProjetoWorkflowController::class, 'avancar'])->name('projetos.workflow.avancar');
});

// final das rotas de workflow BPM

// inicio rotas workflow medição
Route::post('medicoes/{medicao}/documentos/upload', [MedicaoDocumentoController::class, 'upload'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.upload');

Route::post('medicoes/{medicao}/documentos/validar_nf', [MedicaoDocumentoController::class, 'validarNF'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.validar_nf');

Route::post('medicoes/{medicao}/documentos/{doc}/revalidar', [MedicaoDocumentoController::class, 'revalidar'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.revalidar');

Route::post('medicoes/{medicao}/documentos/substituir_nf', [MedicaoDocumentoController::class, 'substituirNF'])
    ->middleware('can.action:medicoes_validar')
    ->name('medicoes.documentos.substituir_nf');

// fim das rotas workflow medição
// inicio das rotas de documentos de medição

Route::prefix('medicoes/{medicao}')->group(function () {
    Route::post('documentos/upload', [MedicaoDocumentoController::class, 'upload'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.upload');
    Route::post('documentos/validar-nf', [MedicaoDocumentoController::class, 'validarNF'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.validar_nf');
    Route::post('documentos/{doc}/revalidar', [MedicaoDocumentoController::class, 'revalidar'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.revalidar');
    Route::post('documentos/substituir-nf', [MedicaoDocumentoController::class, 'substituirNF'])
        ->middleware('can.action:medicoes_validar')
        ->name('medicoes.documentos.substituir_nf');
    Route::get('comparacao', [MedicaoDocumentoController::class, 'comparacao'])->name('medicoes.documentos.comparacao');
});
// final das rotas de documentos de medição
// inicio rotas demandas

Route::post('demandas/{demanda}/requisitos', [DemandaController::class, 'addRequisito'])
    ->middleware('can.action:projetos.editar_requisitos')
    ->name('demandas.requisitos.store');
Route::delete('demandas/{demanda}/requisitos/{requisito}', [DemandaController::class, 'deleteRequisito'])
    ->middleware('can.action:projetos.editar_requisitos')
    ->name('demandas.requisitos.destroy');
// final rotas demandas
// Inicio Rotas Contratos
Route::get('contratos/{contrato}/edit', [ContratoController::class, 'edit'])
    ->middleware('can.action:contratos.edit')
    ->name('contratos.edit');
Route::put('contratos/{contrato}', [ContratoController::class, 'update'])
    ->middleware('can.action:contratos.edit')
    ->name('contratos.update');
Route::get('dashboard/contratos/conformidade', [ContratoConformidadeController::class, 'index'])
    ->name('dashboard.contratos.conformidade');
// Upload inteligente (IA) – página e processamento opcional
Route::get('contratos/upload', [ContratoInteligenteController::class, 'uploadForm'])->name('contratos.upload');
Route::post('contratos/upload', [ContratoInteligenteController::class, 'receberUpload'])->name('contratos.upload.receber');
Route::post('contratos/salvar', [ContratoInteligenteController::class, 'salvar'])->name('contratos.salvar');
Route::post('contratos/extrair', [ContratoController::class, 'extrair'])->name('contratos.extrair');
// Upload de PDF vinculado a um contrato já existente
Route::post('contratos/{contrato}/pdf', [ContratoController::class, 'uploadPdf'])
    ->middleware('can.action:contratos.anexar_documento')
    ->name('contratos.pdf.upload');

// Final Rotas contratos
// Rotas de notificação
Route::middleware(['auth'])->group(function () {
    Route::get('/notificacoes', [NotificationController::class, 'index'])
        ->name('notificacoes.index');

    Route::post('/notificacoes/{notificacao}/lida', [NotificationController::class, 'marcarLida'])
        ->name('notificacoes.lida');

    Route::post('/notificacoes/marcar-todas', [NotificationController::class, 'marcarTodas'])
        ->name('notificacoes.todas');

    // Dados em JSON para DataTables
    Route::get('/notificacoes/data', [NotificationController::class, 'data'])
        ->name('notificacoes.data');

    // Envia uma notificação de teste para o usuário autenticado
    Route::post('/notificacoes/teste', [NotificationController::class, 'teste'])
        ->name('notificacoes.teste');

    Route::post('/push/subscribe', function (\Illuminate\Http\Request $request) {
        auth()->user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return ['success' => true];
    })->name('push.subscribe');
});
// final das rotas de notificação

// ROTAS - AJAX
Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);

// Hosts que o Python deve monitorar
Route::get('/hosts-monitor', [HostMonitorController::class, 'listarHosts']);

// Python envia os resultados
Route::post('/monitoramentos/update', [MonitoramentoController::class, 'atualizar']);

// Histórico de um host, usado pelos gráficos
Route::get('/monitoramentos/historico/{id}', [MonitoramentoController::class, 'historico']);
Route::get('/monitoramentos/latencia-geral', function () {
    $media = \App\Models\Monitoramento::latest()
        ->take(100)
        ->avg('latencia');

    $series = \App\Models\Monitoramento::latest()
        ->take(20)
        ->pluck('latencia')
        ->toArray();

    return response()->json([
        'media' => $media ?? 0,
        'series' => array_reverse($series),
    ]);
});
Route::get('/monitoramentos/heatmap', function () {
    $hosts = \App\Models\Host::with(['escola.dre', 'monitoramentos' => function ($q) {
        $q->orderByDesc('ultima_verificacao')->limit(1);
    }])->get();

    $pontos = $hosts->filter(fn ($h) => $h->escola && $h->escola->latitude && $h->escola->longitude)
        ->map(function ($h) {
            $m = $h->monitoramentos->first();

            return [
                'host_id' => $h->id,
                'nome' => $h->nome_conexao,
                'dre' => $h->escola->dre->nome ?? null,
                'escola' => $h->escola->nome ?? null,
                'lat' => (float) $h->escola->latitude,
                'lng' => (float) $h->escola->longitude,
                'online' => $m?->online ?? 0,
                'latencia' => $m?->latencia,
            ];
        })
        ->values();

    return response()->json($pontos);
})->name('api.monitoramentos.heatmap');

Route::get('/monitoramentos/mikrotik/{host}', function (\App\Models\Host $host) {
    $logs = $host->monitoramentos()
        ->orderByDesc('ultima_verificacao')
        ->limit(50)
        ->get(['ultima_verificacao', 'rx_rate', 'tx_rate'])
        ->sortBy('ultima_verificacao'); // reordena asc p/ gráfico

    return response()->json([
        'labels' => $logs->pluck('ultima_verificacao')->map(fn ($d) => $d->format('H:i'))->values(),
        'rx' => $logs->pluck('rx_rate')->values(),
        'tx' => $logs->pluck('tx_rate')->values(),
    ]);
})->name('api.monitoramentos.mikrotik');
Route::get('/api/monitoramentos/heatline', [MonitoramentoController::class, 'apiHeatline'])
    ->name('api.monitoramentos.heatline');
Route::get('/api/monitoramentos/matrix', [MonitoramentoController::class, 'apiMatrix'])
    ->name('api.monitoramentos.matrix');
if (class_exists(\App\Http\Controllers\Api\NocMapController::class)) {
    Route::get('/api/noc/mapa-sla', [\App\Http\Controllers\Api\NocMapController::class, 'mapaSla'])
        ->name('api.noc.mapa-sla');
}

if (class_exists(\App\Http\Controllers\Api\NocStatsController::class)) {
    Route::get('/api/noc/top-downtime', [\App\Http\Controllers\Api\NocStatsController::class, 'topDowntime'])
        ->name('api.noc.top-downtime');
}

Route::prefix('projetos/{projeto}')->group(function () {
    Route::get('apf', [ProjetoApiController::class, 'apf'])->name('api.projetos.apf');
    Route::get('atividades', [ProjetoApiController::class, 'atividades'])->name('api.projetos.atividades');
    Route::get('medicao', [ProjetoApiController::class, 'medicao'])->name('api.projetos.medicao');
    Route::get('boletins', [ProjetoApiController::class, 'boletins'])->name('api.projetos.boletins');

    // Dashboard
    Route::get('dashboard/pf-ust', [ProjetoApiController::class, 'dashboardPfUst'])->name('api.projetos.dashboard.pf_ust');
    Route::get('dashboard/esforco', [ProjetoApiController::class, 'dashboardEsforco'])->name('api.projetos.dashboard.esforco');
});

// Rota API para DataTables / AJAX
Route::get('api/hosts', [HostController::class, 'getHostsJson'])->name('api.hosts');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato']);
Route::get('empenhos/data', [EmpenhoController::class, 'getData'])->name('empenhos.data');
Route::get('/escolas-data', [EscolaController::class, 'getData'])->name('escolas.data');
Route::get('/hosts/dashboard/data', [HostDashboardController::class, 'dadosAjax'])
    ->name('hosts.dashboard.data');
Route::get('/host_testes/historico', [App\Http\Controllers\HostDashboardController::class, 'historicoAjax'])
    ->name('host_testes.historico');
Route::get('/api/contratos', [App\Http\Controllers\ContratoController::class, 'getJsonContratos'])
    ->name('api.contratos');
Route::get('/api/contratos/detalhes/{id}', [App\Http\Controllers\ContratoController::class, 'detalhesContrato'])
    ->name('api.contratos.detalhes');
Route::get('/api/situacoes', [App\Http\Controllers\SituacaoContratoController::class, 'listar'])
    ->name('api.situacoes');
Route::get('/api/escolas', [MapaController::class, 'escolasGeoJson'])->name('api.escolas');
Route::get('/api/contratos/{id}/itens', [HostController::class, 'getItensPorContrato'])
    ->name('api.contratos.itens');
Route::get('/ajax/contratos/{id}', [ContratoController::class, 'getContratoJson'])
    ->withoutMiddleware(['auth'])
    ->name('ajax.contrato');
Route::get('contratos/{id}/itens', [ContratoController::class, 'getItens'])
    ->name('contratos.itens');
// 🔹 Rota auxiliar (JSON de detalhes para modal)
Route::get('/escolas/{id}/detalhes', [EscolaController::class, 'detalhes'])
    ->name('escolas.detalhes');
// DataTables (lista de servidores)
Route::get('/api/servidores', [ServidorController::class, 'index'])->name('api.servidores.index');
// FINAL DE ROTAS DE API AJAX
// JasperReports demo
// Removido demo JasperReports

Route::middleware(['auth'])->group(function () {

    // Permitir acesso ao dashboard IA para Admin, Gestor e Fiscais
    Route::middleware(['role:Administrador,Gestor de Contrato,admin,gestor,Fiscal,fiscal_administrativo,fiscal_tecnico'])->group(function () {
        Route::get('/ia-dashboard', function () {
            return view('fiscalizer-ia');
        })->name('fiscalizer-ia.index');
    });

    // Ordens de Fornecimento
    Route::middleware(['role:Administrador,Gestor de Contrato,Fiscal'])
        ->prefix('ordens-fornecimento')
        ->name('ordens_fornecimento.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\OrdemFornecimentoController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\OrdemFornecimentoController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [\App\Http\Controllers\OrdemFornecimentoController::class, 'pdf'])->name('pdf');
        });

    // Relatórios: contratos (PDF via DomPDF)
    Route::get('/reports/contratos', [\App\Http\Controllers\JasperReportsController::class, 'contratos'])
        ->name('reports.contratos');

    // Preview do Termo de Referência (layout Blade)
    Route::get('/reports/termo-referencia', [TermoReferenciaController::class, 'preview'])
        ->name('reports.tr.preview');

    // Relatórios por templates (Word/Excel)
    Route::post('/reports/templates/docx', [\App\Http\Controllers\TemplateReportsController::class, 'generateDocx'])
        ->name('reports.templates.docx');
    Route::post('/reports/templates/xlsx', [\App\Http\Controllers\TemplateReportsController::class, 'generateXlsx'])
        ->name('reports.templates.xlsx');

});
// Admin: Notificações (eventos/templates)
Route::middleware(['auth', 'can.action:system.admin'])
    ->prefix('admin/notificacoes')
    ->name('admin.notificacoes.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationEventController::class, 'index'])->name('index');
        Route::get('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'show'])->name('show');
        Route::get('/create', [\App\Http\Controllers\NotificationEventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\NotificationEventController::class, 'store'])->name('store');
        Route::get('/{evento}/edit', [\App\Http\Controllers\NotificationEventController::class, 'edit'])->name('edit');
        Route::put('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'update'])->name('update');
        Route::delete('/{evento}', [\App\Http\Controllers\NotificationEventController::class, 'destroy'])->name('destroy');

        Route::post('/import', [\App\Http\Controllers\NotificationEventController::class, 'importFromConfig'])->name('import');
        Route::post('/sync', [\App\Http\Controllers\NotificationEventController::class, 'syncActions'])->name('sync');
        Route::get('/users/search', [\App\Http\Controllers\NotificationEventController::class, 'searchUsers'])->name('users.search');


    });

// Inventário por Unidade (somente perfil regional)
Route::middleware(['auth','can:inventario.unidades.gerenciar'])->group(function () {
    Route::get('/inventario/unidades', [\App\Http\Controllers\InventarioController::class, 'selecionarUnidade'])
        ->name('inventario.unidades.select');
    Route::post('/inventario/dres/{dre}/acessar', [\App\Http\Controllers\InventarioController::class, 'acessarPorDre'])
        ->name('inventario.dres.acessar');
    Route::get('/unidades/{unidade}/inventario', [\App\Http\Controllers\InventarioController::class, 'index'])
        ->name('unidades.inventario');
    Route::post('/unidades/{unidade}/inventario', [\App\Http\Controllers\InventarioController::class, 'store'])
        ->name('unidades.inventario.store');
    Route::post('/equipamentos/{equipamento}/quebra', [\App\Http\Controllers\InventarioController::class, 'reportarQuebra'])
        ->name('equipamentos.quebra');
    Route::post('/unidades/{unidade}/reposicoes', [\App\Http\Controllers\InventarioController::class, 'solicitarReposicao'])
        ->name('unidades.reposicoes.solicitar');
    Route::post('/unidades/{unidade}/conexoes', [\App\Http\Controllers\InventarioController::class, 'storeConexao'])
        ->name('unidades.conexoes.store');
    Route::get('/unidades/{unidade}/especificacoes', [\App\Http\Controllers\InventarioController::class, 'gerarEspecificacoes'])
        ->name('unidades.especificacoes');
});

// Monitoramento dos Agentes (inventário)
Route::get('/inventario/monitoramento', [\App\Http\Controllers\TelemetriaDashboardController::class, 'index'])
    ->middleware(['auth','can:ver-inventario'])
    ->name('inventario.monitoramento');
Route::middleware(['auth'])->group(function () {
    Route::post('unidades/{unidade}/normas/upload', [InvController::class, 'uploadNorma'])
        ->name('unidades.normas.upload');

    Route::post('ocorrencias/{ocorrencia}/cit/receber', [InvController::class, 'citReceberOcorrencia'])
        ->middleware(['auth','role:CIT,Administrador'])
        ->name('ocorrencias.cit.receber');
    Route::post('ocorrencias/{ocorrencia}/cit/avaliar', [InvController::class, 'citAvaliarOcorrencia'])
        ->middleware(['auth','role:CIT,Administrador'])
        ->name('ocorrencias.cit.avaliar');

    Route::post('reposicoes/{reposicao}/detec/aprovar', [InvController::class, 'detecAprovarReposicao'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.aprovar');
    Route::post('reposicoes/{reposicao}/detec/entregar', [InvController::class, 'detecRegistrarEntrega'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.entregar');
    Route::post('reposicoes/{reposicao}/detec/baixar/{equipamento}', [InvController::class, 'detecBaixarEquipamento'])
        ->middleware(['auth','role:DETEC,Administrador'])
        ->name('reposicoes.detec.baixar');
});
// Scrap CSV
Route::get('scrap/test', [\App\Http\Controllers\ScrapController::class, 'index'])->name('scrap.test');
Route::post('scrap/fetch', [\App\Http\Controllers\ScrapController::class, 'fetch'])->name('scrap.fetch');
Route::post('scrap/swagger', [\App\Http\Controllers\ScrapController::class, 'fetchSwagger'])->name('scrap.swagger');
