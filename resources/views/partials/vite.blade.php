@php
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Vite;

    // Inputs we expect from layouts
    $inputs = $inputs ?? ['resources/css/app.css', 'resources/js/app.js'];

    // Detect running Vite dev server (ignore stale hot file)
    $hotPath = public_path('hot');
    $devOk = false;
    $hotBase = null;

    if (File::exists($hotPath)) {
        try {
            $hotBase = trim(File::get($hotPath));
            $hotBase = str_replace('[::1]', '127.0.0.1', $hotBase);
            $pingUrl = rtrim($hotBase, '/').'/@vite/client';
            $ctx = stream_context_create(['http' => ['timeout' => 0.25]]);
            $ping = @file_get_contents($pingUrl, false, $ctx);
            $devOk = $ping !== false;
        } catch (\Throwable $e) {
            $devOk = false;
        }
    }

    // Helper: read manifest when available
    $manifestPath = public_path('build/manifest.json');
    $manifest = File::exists($manifestPath)
        ? json_decode(File::get($manifestPath), true)
        : null;

    $cssInputs = array_values(array_filter((array) $inputs, fn ($i) => str_ends_with($i, '.css')));
    $jsInputs  = array_values(array_filter((array) $inputs, fn ($i) => !str_ends_with($i, '.css')));

    // Only use the Vite dev server when accessed from localhost.
    // For tunnels / other hosts, always prefer built assets so URLs don't
    // point at the visitor's own localhost.
    $host = request()->getHost();
    $useDevServer = $devOk && in_array($host, ['127.0.0.1', 'localhost', '::1']);
@endphp

{{-- Prefer built CSS even when dev server is live --}}
@if ($manifest)
    {{-- Load CSS from manifest --}}
    @foreach ($cssInputs as $css)
        @php $entry = $manifest[$css] ?? null; @endphp
        @if ($entry && isset($entry['file']))
            <link rel="stylesheet" href="/build/{{ $entry['file'] }}">
        @endif
    @endforeach
    {{-- JS-emitted CSS --}}
    @foreach ($jsInputs as $js)
        @php $entry = $manifest[$js] ?? null; @endphp
        @if ($entry && !empty($entry['css']))
            @foreach ($entry['css'] as $cssFile)
                <link rel="stylesheet" href="/build/{{ $cssFile }}">
            @endforeach
        @endif
    @endforeach
@elseif($useDevServer && isset($hotBase))
    {{-- No manifest yet, but dev server is live → load CSS from dev --}}
    @foreach ($cssInputs as $css)
        <link rel="stylesheet" href="{{ rtrim($hotBase,'/') }}/{{ ltrim($css,'/') }}">
    @endforeach
@else
    {{-- Final safety net: Tailwind + Alpine from CDNs so pages still look styled --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif

{{-- JS: HMR in dev, built files otherwise --}}
@if ($useDevServer)
    @vite($jsInputs)
@else
    @if ($manifest)
        @foreach ($jsInputs as $js)
            @php $entry = $manifest[$js] ?? null; @endphp
            @if ($entry && isset($entry['file']))
                <script type="module" src="/build/{{ $entry['file'] }}"></script>
            @endif
        @endforeach
    @endif
@endif
