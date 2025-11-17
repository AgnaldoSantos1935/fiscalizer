<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Imprimir Documento</title>
  <style>
    html, body { height: 100%; margin: 0; }
    .frame { position: fixed; inset: 0; border: 0; width: 100%; height: 100%; }
  </style>
</head>
<body>
  <iframe id="printFrame" class="frame" src="{{ route('documentos.stream', $documento) }}" title="PDF"></iframe>
  <script>
    (function() {
      const iframe = document.getElementById('printFrame');
      const returnTo = "{{ request('return_to') }}";

      // Após a impressão, retorna para a página indicada ou volta no histórico
      window.onafterprint = function() {
        if (returnTo) {
          window.location.href = returnTo;
        } else {
          history.back();
        }
      };
      // Tenta imprimir quando o iframe carregar
      iframe.addEventListener('load', function() {
        try {
          const win = iframe.contentWindow;
          if (win && typeof win.print === 'function') {
            win.focus();
            win.print();
          } else {
            window.print(); // fallback
          }
        } catch (e) {
          console.warn('Falha ao acionar impressão via iframe:', e);
          window.print();
        }
      });
      // Como fallback adicional, tenta imprimir após um pequeno atraso
      setTimeout(function() {
        try { window.print(); } catch (e) {}
      }, 1500);
    })();
  </script>
</body>
</html>