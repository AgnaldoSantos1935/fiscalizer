<!DOCTYPE html><html><head><meta charset="utf-8"><title>Autorização de Adesão</title><style>body{font-family:Arial}h1{font-size:18px}table{width:100%;border-collapse:collapse}td,th{padding:6px;border:1px solid #ccc}</style></head><body>
<h1>Autorização de Adesão à Ata de Registro de Preços</h1>
<p>Ata: {{ $adesao->ata->numero }} — Objeto: {{ $adesao->ata->objeto }}</p>
<p>Órgão Gerenciador: {{ $adesao->ata->orgaoGerenciador->razao_social ?? '' }}</p>
<p>Fornecedor: {{ $adesao->ata->fornecedor->razao_social ?? '' }}</p>
<p>Órgão Adquirente: {{ $adesao->orgaoAdquirente->razao_social ?? '' }}</p>
<p>Justificativa: {{ $adesao->justificativa }}</p>
<p>Vigência da Ata: {{ optional($adesao->ata->vigencia_inicio)->format('d/m/Y') }} a {{ optional($adesao->ata->vigencia_final)->format('d/m/Y') }}</p>
<p>Status da Adesão: {{ $adesao->status }}</p>
<table>
<tr><th>Valor Estimado</th><td>R$ {{ number_format($adesao->valor_estimado ?? 0, 2, ',', '.') }}</td></tr>
<tr><th>Data Solicitação</th><td>{{ optional($adesao->data_solicitacao)->format('d/m/Y') }}</td></tr>
<tr><th>Base Legal</th><td>Lei nº 14.133/2021</td></tr>
</table>
</body></html>

