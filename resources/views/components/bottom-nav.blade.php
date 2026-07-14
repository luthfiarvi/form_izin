@php
    $auth = auth()->check();
    $isHome    = request()->routeIs('izin.create');
    $isData    = request()->routeIs('izin.data');
    $isProfile = request()->routeIs('profile.*');
@endphp

<nav class="bottom-nav fixed bottom-0 left-0 right-0 z-40" style="background:linear-gradient(135deg,#0f2d3d 0%,#1a4a5e 100%);border-top:1px solid rgba(255,255,255,0.07);box-shadow:0 -4px 20px rgba(0,0,0,0.22);">
    <ul style="display:grid;grid-template-columns:repeat(4,1fr);text-align:center;">

        {{-- Home --}}
        <li>
            <a href="{{ $auth ? route('izin.create', [], false) : route('login', [], false) }}"
               class="bottom-nav-item {{ $isHome ? 'active' : '' }}"
               style="padding:10px 4px 8px;display:flex;flex-direction:column;align-items:center;gap:3px;text-decoration:none;transition:all 0.2s ease;color:{{ $isHome ? '#f59e0b' : 'rgba(255,255,255,0.5)' }};position:relative;">
                @if($isHome)
                <span style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:32px;height:3px;background:#f59e0b;border-radius:0 0 4px 4px;"></span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;transition:transform 0.2s ease;{{ $isHome ? 'transform:translateY(-2px)' : '' }}" fill="{{ $isHome ? '#f59e0b' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isHome ? '0' : '1.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span style="font-size:10px;font-weight:{{ $isHome ? '700' : '500' }};letter-spacing:0.03em;">Home</span>
            </a>
        </li>

        {{-- Data Izin --}}
        <li>
            <a href="{{ $auth ? route('izin.data', [], false) : route('login', [], false) }}"
               class="bottom-nav-item {{ $isData ? 'active' : '' }}"
               style="padding:10px 4px 8px;display:flex;flex-direction:column;align-items:center;gap:3px;text-decoration:none;transition:all 0.2s ease;color:{{ $isData ? '#f59e0b' : 'rgba(255,255,255,0.5)' }};position:relative;">
                @if($isData)
                <span style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:32px;height:3px;background:#f59e0b;border-radius:0 0 4px 4px;"></span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;transition:transform 0.2s ease;{{ $isData ? 'transform:translateY(-2px)' : '' }}" fill="{{ $isData ? '#f59e0b' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isData ? '0' : '1.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span style="font-size:10px;font-weight:{{ $isData ? '700' : '500' }};letter-spacing:0.03em;">Data Izin</span>
            </a>
        </li>

        {{-- Profile --}}
        <li>
            <a href="{{ $auth ? route('profile.edit', [], false) : route('login', [], false) }}"
               class="bottom-nav-item {{ $isProfile ? 'active' : '' }}"
               style="padding:10px 4px 8px;display:flex;flex-direction:column;align-items:center;gap:3px;text-decoration:none;transition:all 0.2s ease;color:{{ $isProfile ? '#f59e0b' : 'rgba(255,255,255,0.5)' }};position:relative;">
                @if($isProfile)
                <span style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:32px;height:3px;background:#f59e0b;border-radius:0 0 4px 4px;"></span>
                @endif
                <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;transition:transform 0.2s ease;{{ $isProfile ? 'transform:translateY(-2px)' : '' }}" fill="{{ $isProfile ? '#f59e0b' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $isProfile ? '0' : '1.8' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span style="font-size:10px;font-weight:{{ $isProfile ? '700' : '500' }};letter-spacing:0.03em;">{{ $auth ? 'Profil' : 'Login' }}</span>
            </a>
        </li>

        {{-- Logout / Register --}}
        <li>
            @if ($auth)
                <form method="POST" action="{{ route('logout', [], false) }}" style="display:flex;flex-direction:column;align-items:center;">
                    @csrf
                    <button type="submit"
                            style="padding:10px 4px 8px;display:flex;flex-direction:column;align-items:center;gap:3px;background:none;border:none;cursor:pointer;transition:all 0.2s ease;color:rgba(255,255,255,0.5);width:100%;"
                            onmouseenter="this.style.color='rgba(255,255,255,0.9)'" onmouseleave="this.style.color='rgba(255,255,255,0.5)'">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span style="font-size:10px;font-weight:500;letter-spacing:0.03em;">Keluar</span>
                    </button>
                </form>
            @else
                <a href="{{ route('register', [], false) }}"
                   style="padding:10px 4px 8px;display:flex;flex-direction:column;align-items:center;gap:3px;text-decoration:none;transition:all 0.2s ease;color:rgba(255,255,255,0.5);"
                   onmouseenter="this.style.color='rgba(255,255,255,0.9)'" onmouseleave="this.style.color='rgba(255,255,255,0.5)'">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:22px;height:22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span style="font-size:10px;font-weight:500;letter-spacing:0.03em;">Daftar</span>
                </a>
            @endif
        </li>
    </ul>
</nav>
