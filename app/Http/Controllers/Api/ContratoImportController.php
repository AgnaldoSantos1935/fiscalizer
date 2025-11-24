<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContratoImportController extends Controller
{
    public function import(Request $request)
    {
        $payloadRaw = $request->json()->all();
        if (! is_array($payloadRaw)) {
            $payload = [$payloadRaw];
        } else {
            $keys = array_keys($payloadRaw);
            $hasStringKey = false;
            foreach ($keys as $k) { if (is_string($k)) { $hasStringKey = true; break; } }
            $payload = $hasStringKey ? [$payloadRaw] : $payloadRaw;
        }

        $created = 0;
        $updated = 0;
        $errors = [];
        $warnings = [];

        $payloadCount = count($payload);
        \Illuminate\Support\Facades\DB::connection()->disableQueryLog();
        $chunkSize = 500;

        for ($offset = 0; $offset < $payloadCount; $offset += $chunkSize) {
            $batch = array_slice($payload, $offset, $chunkSize, true);
            foreach ($batch as $idx => $data) {
            if (! is_array($data)) {
                $errors[] = [
                    'index' => $idx,
                    'numero' => null,
                    'messages' => ['Formato inválido: cada registro deve ser um objeto JSON'],
                ];
                continue;
            }

            $norm = $data;
            if (! isset($norm['valor']) && isset($norm['valor_global'])) {
                $norm['valor'] = ['valor_global' => $norm['valor_global']];
            }
            if (! isset($norm['vigencia'])) {
                $vg = [];
                if (isset($norm['data_inicio'])) { $vg['data_inicio'] = $norm['data_inicio']; }
                if (isset($norm['data_fim'])) { $vg['data_fim'] = $norm['data_fim']; }
                if (! empty($vg)) { $norm['vigencia'] = $vg; }
            }
            if (! isset($norm['contratada'])) {
                $ct = [];
                if (isset($norm['contratada_id'])) { $ct['empresa_id'] = $norm['contratada_id']; }
                if (isset($norm['cnpj'])) { $ct['cnpj'] = $norm['cnpj']; }
                if (isset($norm['razao_social'])) { $ct['razao_social'] = $norm['razao_social']; }
                if (! empty($ct)) { $norm['contratada'] = $ct; }
            }
            if (! empty($norm['itens']) && is_array($norm['itens'])) {
                $norm['itens'] = array_map(function ($item) {
                    if (is_array($item)) {
                        if (! isset($item['descricao']) && isset($item['descricao_item'])) { $item['descricao'] = $item['descricao_item']; }
                        if (! isset($item['unidade']) && isset($item['unidade_medida'])) { $item['unidade'] = $item['unidade_medida']; }
                    }
                    return $item;
                }, $norm['itens']);
            }
            $data = $norm;

            $val = Validator::make($data, [
                'numero' => 'required|string|max:50',
                'objeto' => 'required|string',
                'valor.valor_global' => 'nullable|numeric',
            ]);
            if ($val->fails()) {
                $errors[] = [
                    'index' => $idx,
                    'numero' => $data['numero'] ?? null,
                    'messages' => $val->errors()->all(),
                ];
                continue;
            }

            try {
                $recordWarnings = [];

                $vigencia = is_array($data['vigencia'] ?? null) ? $data['vigencia'] : [];
                $inicio = $vigencia['data_inicio'] ?? null;
                $fim = $vigencia['data_fim'] ?? null;
                if ($inicio && $fim) {
                    if (strtotime($fim) < strtotime($inicio)) {
                        $errors[] = [
                            'index' => $idx,
                            'numero' => $data['numero'] ?? null,
                            'messages' => ['Data fim anterior à data início'],
                        ];
                        continue;
                    }
                }

                $contratada = is_array($data['contratada'] ?? null) ? $data['contratada'] : [];
                $empresaIdCheck = $contratada['empresa_id'] ?? null;
                $cnpjCheck = $contratada['cnpj'] ?? null;
                $razaoCheck = $contratada['razao_social'] ?? null;
                if (! $empresaIdCheck && ! $cnpjCheck && ! $razaoCheck) {
                    $errors[] = [
                        'index' => $idx,
                        'numero' => $data['numero'] ?? null,
                        'messages' => ['Contratada ausente: informe empresa_id ou cnpj/razao_social'],
                    ];
                    continue;
                }
                if ($cnpjCheck) {
                    $d = preg_replace('/\D+/', '', (string) $cnpjCheck);
                    if (strlen($d) !== 14) {
                        $errors[] = [
                            'index' => $idx,
                            'numero' => $data['numero'] ?? null,
                            'messages' => ['CNPJ inválido para contratada'],
                        ];
                        continue;
                    }
                }

                $valor = is_array($data['valor'] ?? null) ? $data['valor'] : [];
                if (! empty($data['itens']) && is_array($data['itens'])) {
                    foreach ($data['itens'] as $k => $item) {
                        $q = $item['quantidade'] ?? 0;
                        $vu = $item['valor_unitario'] ?? 0;
                        if ($q < 0 || $vu < 0) {
                            $errors[] = [
                                'index' => $idx,
                                'numero' => $data['numero'] ?? null,
                                'messages' => ["Item {$k}: quantidade/valor_unitario não pode ser negativo"],
                            ];
                            continue 2;
                        }
                        if (isset($item['valor_total'])) {
                            $vt = (float) $item['valor_total'];
                            $calc = (float) ($q * $vu);
                            if (abs($vt - $calc) > 0.01) {
                                $recordWarnings[] = "Item {$k}: valor_total divergente do cálculo ({$vt} != {$calc})";
                            }
                        }
                    }
                    $sum = 0.0;
                    foreach ($data['itens'] as $it) {
                        $sum += (float) (($it['quantidade'] ?? 0) * ($it['valor_unitario'] ?? 0));
                    }
                    $vg = $valor['valor_global'] ?? null;
                    if ($vg !== null) {
                        $vgf = (float) $vg;
                        if (abs($vgf - $sum) > 0.01) {
                            $recordWarnings[] = 'Valor global divergente da soma dos itens';
                        }
                    }
                }

                DB::transaction(function () use (&$created, &$updated, &$warnings, $recordWarnings, $data, $contratada, $vigencia, $valor) {
                    $contrato = Contrato::where('numero', $data['numero'])->first();

                    $empresaId = $contratada['empresa_id'] ?? null;
                    if (! $empresaId) {
                        $cnpj = isset($contratada['cnpj']) ? preg_replace('/\D+/', '', (string) $contratada['cnpj']) : null;
                        if ($cnpj) {
                            $empresaId = Empresa::where('cnpj', $cnpj)->value('id');
                        }
                        if (! $empresaId && ! empty($contratada['razao_social'])) {
                            $enderecoData = is_array($contratada['endereco'] ?? null) ? $contratada['endereco'] : null;
                            $enderecoString = null;
                            if ($enderecoData) {
                                $p1 = trim(($enderecoData['logradouro'] ?? '').' '.($enderecoData['numero'] ?? ''));
                                $p2 = trim($enderecoData['complemento'] ?? '');
                                $p3 = trim($enderecoData['bairro'] ?? '');
                                $p4 = trim(($enderecoData['cidade'] ?? '').' '.($enderecoData['uf'] ?? ''));
                                $p5 = trim($enderecoData['cep'] ?? '');
                                $parts = array_filter([$p1, $p2, $p3, $p4], fn($v) => $v !== '');
                                $enderecoString = implode(', ', $parts);
                                if ($p5 !== '') { $enderecoString = $enderecoString ? ($enderecoString.' - CEP '.$p5) : ('CEP '.$p5); }
                            }

                            $empresa = Empresa::firstOrCreate([
                                'cnpj' => $cnpj,
                            ], [
                                'razao_social' => $contratada['razao_social'],
                                'nome_fantasia' => $contratada['razao_social'] ?? null,
                                'endereco' => $enderecoData ? $enderecoString : ($contratada['endereco'] ?? null),
                                'logradouro' => $enderecoData['logradouro'] ?? null,
                                'numero' => $enderecoData['numero'] ?? null,
                                'complemento' => $enderecoData['complemento'] ?? null,
                                'bairro' => $enderecoData['bairro'] ?? null,
                                'cidade' => $enderecoData['cidade'] ?? null,
                                'uf' => $enderecoData['uf'] ?? null,
                                'cep' => $enderecoData['cep'] ?? null,
                            ]);
                            $empresaId = $empresa->id;
                        }
                    }

                    $attrs = [
                        'numero' => $data['numero'],
                        'ano' => $data['ano'] ?? null,
                        'processo_administrativo' => $data['processo_administrativo'] ?? null,
                        'fundamentacao_legal' => $data['fundamentacao_legal'] ?? null,
                        'objeto' => $data['objeto'],
                        'valor_global' => $valor['valor_global'] ?? null,
                        'data_inicio' => $vigencia['data_inicio'] ?? null,
                        'data_fim' => $vigencia['data_fim'] ?? null,
                        'contratada_id' => $empresaId,

                        'contratante_json' => $data['contratante'] ?? null,
                        'contratada_representante_json' => $contratada['representante'] ?? null,
                        'vigencia_info_json' => [
                            'prazo_meses' => $vigencia['prazo_meses'] ?? null,
                            'tipo_prazo' => $vigencia['tipo_prazo'] ?? null,
                        ],
                        'dotacao_orcamentaria_json' => $data['dotacao_orcamentaria'] ?? null,
                        'reajuste_json' => $data['reajuste'] ?? null,
                        'garantia_json' => $data['garantia'] ?? null,
                        'pagamento_json' => $data['pagamento'] ?? null,
                        'fiscalizacao_json' => $data['fiscalizacao'] ?? null,
                        'penalidades_json' => $data['penalidades'] ?? null,
                        'rescisao_json' => $data['rescisao'] ?? null,
                        'lgpd_json' => $data['lgpd'] ?? null,
                        'publicacao_doe_json' => $data['publicacao_doe'] ?? null,
                    ];

                    if ($contrato) {
                        $contrato->update($attrs);
                        $updated++;
                    } else {
                        $contrato = Contrato::create($attrs);
                        $created++;
                    }

                    if (! empty($data['itens']) && is_array($data['itens'])) {
                        // Recria itens conforme payload
                        $contrato->itens()->delete();
                        foreach ($data['itens'] as $item) {
                            $contrato->itens()->create([
                                'descricao_item' => $item['descricao'] ?? null,
                                'unidade_medida' => $item['unidade'] ?? null,
                                'quantidade' => $item['quantidade'] ?? 0,
                                'valor_unitario' => $item['valor_unitario'] ?? 0,
                                'tipo_item' => 'servico',
                                'status' => 'ativo',
                            ]);
                        }
                    }
                    if (! empty($recordWarnings)) {
                        $warnings[] = [
                            'numero' => $data['numero'] ?? null,
                            'messages' => $recordWarnings,
                        ];
                    }
                });
            } catch (\Throwable $e) {
                $errors[] = [
                    'index' => $idx,
                    'numero' => $data['numero'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }
        }
        }

        return response()->json([
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}
