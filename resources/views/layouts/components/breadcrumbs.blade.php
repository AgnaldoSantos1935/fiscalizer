{{-- ============================================================
     BREADCRUMB GOVBR – FISCALIZER
   ============================================================ --}}

<nav aria-label="breadcrumb" class="mb-2">
    <ol class="breadcrumb bg-white px-0 py-2">

        <li class="breadcrumb-item">
            <a href="{{ route('home') }}" class="text-primary">
                <i class="fas fa-home"></i>
            </a>
        </li>

        @php($__items = $breadcrumbs ?? $trail ?? [])
        @foreach ($__items as $item)
            @if (!$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] ?? '#' }}" class="text-primary">{{ $item['label'] ?? '' }}</a>
                </li>
            @else
                <li class="breadcrumb-item active text-dark" aria-current="page">
                    {{ $item['label'] ?? '' }}
                </li>
            @endif
        @endforeach

    </ol>
</nav>
