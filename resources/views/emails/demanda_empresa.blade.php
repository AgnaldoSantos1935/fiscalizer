<!DOCTYPE html>
<html lang="pt-br">
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">

<div style="padding: 20px; background: #f4f4f4;">
    <div style="background: #fff; padding: 20px; border-radius: 8px;">

        <div style="text-align: center;">
            <img src="{{ asset('images/brasao-pa.png') }}" width="80" style="margin-bottom: 15px;">
        </div>

        <p>Prezados,</p>

        <p>
            A Secretaria de Estado de Educação do Pará (SEDUC/PA), por meio da Diretoria de Tecnologia – DETEC,
            informa que foi registrada uma nova demanda de desenvolvimento/manutenção de software.
        </p>

        <p><strong>Nº da Demanda:</strong> {{ $demanda->id }}</p>
        <p><strong>Título:</strong> {{ $demanda->titulo }}</p>
        <p><strong>Tipo:</strong> {{ $demanda->tipo_manutencao }}</p>

        <p>
            Solicitamos o envio do <strong>Documento Técnico Consolidado</strong> contendo:
        </p>

        <ul>
            <li>Levantamento de requisitos</li>
            <li>Protótipos de telas</li>
            <li>Cronograma</li>
            <li>Estimativa de PF e UST</li>
            <li>Escopo detalhado e artefatos</li>
        </ul>

        <p>
            Para enviar o documento técnico, acesse o link abaixo:
        </p>

        <p style="text-align: center;">
            <a href="{{ $urlUpload }}"
               style="background: #007bff; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 6px;">
                ENVIAR DOCUMENTO TÉCNICO
            </a>
        </p>

        <p class="mt-4">
            Em caso de dúvidas, entre em contato com a equipe técnica da DETEC.
        </p>

        <p>Atenciosamente,<br>
        <strong>Diretoria de Tecnologia – DETEC</strong><br>
        Secretaria de Estado de Educação do Pará – SEDUC/PA</p>

    </div>
</div>

</body>
</html>
