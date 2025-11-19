@php
    $auth = auth()->check();
@endphp

<nav class="fixed bottom-0 left-0 right-0 bg-[#96A78D] text-white border-t border-emerald-900/40 shadow-[0_-2px_10px_rgba(0,0,0,0.15)] z-40">
    <ul class="grid grid-cols-4 text-center text-xs">
        {{-- Home --}}
        <li class="py-1 flex flex-col items-center">
            @php $isHome = request()->routeIs('izin.create'); @endphp
            <a href="{{ $auth ? route('izin.create', [], false) : route('login', [], false) }}"
               class="inline-flex items-center justify-center cursor-pointer">
                <span class="text-2xl leading-none {{ $isHome ? 'text-white' : 'text-emerald-50/80' }}">🏠</span>
            </a>
            <span class="mt-1 {{ $isHome ? 'text-white font-semibold' : 'text-emerald-50/90' }}">Home</span>
        </li>

        {{-- Data Izin --}}
        <li class="py-1 flex flex-col items-center">
            @php $isData = request()->routeIs('izin.data'); @endphp
            <a href="{{ $auth ? route('izin.data', [], false) : route('login', [], false) }}"
               class="inline-flex items-center justify-center cursor-pointer">
                <span class="text-2xl leading-none {{ $isData ? 'text-white' : 'text-emerald-50/80' }}">📊</span>
            </a>
            <span class="mt-1 {{ $isData ? 'text-white font-semibold' : 'text-emerald-50/90' }}">Data Izin</span>
        </li>

        {{-- Profile / Login --}}
        <li class="py-1 flex flex-col items-center">
            @php $isProfile = request()->routeIs('profile.*'); @endphp
            <a href="{{ $auth ? route('profile.edit', [], false) : route('login', [], false) }}"
               class="inline-flex items-center justify-center cursor-pointer">
                <span class="text-2xl leading-none {{ $isProfile ? 'text-white' : 'text-emerald-50/80' }}">👤</span>
            </a>
            <span class="mt-1 {{ $isProfile ? 'text-white font-semibold' : 'text-emerald-50/90' }}">{{ $auth ? 'User' : 'Login' }}</span>
        </li>

        {{-- Logout / Register --}}
        <li class="py-1 flex flex-col items-center">
            @if ($auth)
                <form method="POST" action="{{ route('logout', [], false) }}" class="flex flex-col items-center">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center text-emerald-50/90 hover:text-white cursor-pointer">
                        <span class="text-2xl leading-none">🚪</span>
                    </button>
                    <span class="mt-1 text-emerald-50/90">Keluar</span>
                </form>
            @else
                <a href="{{ route('register', [], false) }}"
                   class="inline-flex items-center justify-center cursor-pointer">
                    <span class="text-2xl leading-none text-emerald-50/90">➕</span>
                </a>
                <span class="mt-1 text-emerald-50/90">Daftar</span>
            @endif
        </li>
    </ul>
</nav>

