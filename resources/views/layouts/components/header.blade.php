<div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-3">

    <div>
        <h2 class="m-0 fw-bold text-primary">@yield('page_title')</h2>

        @hasSection('page_subtitle')
            <small class="text-muted">@yield('page_subtitle')</small>
        @endif
    </div>

    <div class="d-flex gap-2">
        @yield('page_actions')
    </div>

</div>
