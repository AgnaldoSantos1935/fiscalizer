<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Relatório')</title>
    <style>
        @page { margin: 90px 40px 60px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { position: fixed; top: -70px; left: 0; right: 0; height: 70px; }
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 40px; }
        .header .row, .footer .row { display: flex; justify-content: space-between; align-items: center; }
        .brand { font-weight: bold; font-size: 14px; }
        .muted { color: #666; }
        .page { font-size: 11px; color: #555; }
        .page:after { content: "Página " counter(page) " / " counter(pages); }
        h1 { font-size: 16px; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; text-align: left; }
        .nowrap { white-space: nowrap; }
        main { page-break-inside: auto; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="header">
        @hasSection('header')
            @yield('header')
        @else
            <div class="row">
                <div class="brand">@yield('header_left', 'Fiscalizer')</div>
                <div class="muted">@yield('header_right')</div>
            </div>
        @endif
    </div>

    <div class="footer">
        @hasSection('footer')
            @yield('footer')
        @else
            <div class="row">
                <div class="muted">Gerado em {{ date('d/m/Y H:i') }}</div>
                <div class="page"></div>
            </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>
</body>
</html>