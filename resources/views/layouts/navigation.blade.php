<nav x-data="{ open: false }" class="bg-brand-dark text-white shadow-md">
    @php
        $navUser = Auth::user();
        $navRole = $navUser->role ?? null;
        $isAdminLike = $navUser && (in_array($navRole, ['admin', 'hr'], true) || (bool) ($navUser->is_kepala_kepegawaian ?? false));
    @endphp
    <!-- Primary Navigation Menu -->
    <div class="max-w-6xl mx-auto px-6 sm:px-8 lg:px-10">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard', [], false) }}">
                        <x-application-logo class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links (currently none) -->
            </div>

            <!-- Settings Dropdown (x-dropdown + mini profile) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="auto">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-brand-dark bg-brand-accent hover:bg-yellow-400 hover:text-brand-dark focus:outline-none transition ease-in-out duration-150">
                            @if ($navUser && $navUser->profile_photo_path)
                                <img src="{{ asset('storage/'.$navUser->profile_photo_path) }}"
                                     alt="Profile photo"
                                     class="inline-block w-9 h-9 rounded-full object-cover border border-white/40">
                            @else
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-700 text-white uppercase text-sm">
                                    {{ mb_substr($navUser?->name ?? '', 0, 1, 'UTF-8') }}
                                </span>
                            @endif

                            <div class="truncate max-w-[9rem] text-left">{{ $navUser?->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-emerald-100 text-gray-900 rounded-t-md">
                            <div class="flex items-center gap-3">
                                @if ($navUser && $navUser->profile_photo_path)
                                    <img src="{{ asset('storage/'.$navUser->profile_photo_path) }}"
                                         alt="Profile photo"
                                         class="inline-block w-10 h-10 rounded-full object-cover border border-white shadow-sm">
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-700 text-white uppercase text-sm shadow-sm">
                                        {{ mb_substr($navUser?->name ?? '', 0, 1, 'UTF-8') }}
                                    </span>
                                @endif
                                <div class="ms-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $navUser?->name }}</p>
                                    @if ($navUser?->email)
                                        <p class="text-[11px] text-gray-700 mt-1 leading-tight whitespace-nowrap">
                                            <span class="font-medium">Email:</span>
                                            <span class="ml-1">{{ $navUser->email }}</span>
                                        </p>
                                    @endif
                                    @if ($navUser?->whatsapp_phone)
                                        <p class="text-[11px] text-gray-700 leading-tight whitespace-nowrap">
                                            <span class="font-medium">WA:</span>
                                            <span class="ml-1">{{ $navUser->whatsapp_phone }}</span>
                                        </p>
                                    @endif
                                    @php($navPoints = (int) ($navUser?->points ?? 100))
                                    <p class="mt-1 inline-flex items-center text-[11px] text-emerald-800 bg-emerald-100 border border-emerald-200 rounded-full px-2 py-[1px]">
                                        <span class="font-semibold">Poin:</span>
                                        <span class="ml-1">{{ max(0, $navPoints) }}/100</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="py-1 bg-white rounded-b-md">
                            <x-dropdown-link :href="route('profile.edit', [], false)">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('points.index', [], false)">
                                {{ __('Poin Pelanggaran') }}
                            </x-dropdown-link>

                            @if($isAdminLike)
                                <div class="border-t border-gray-100 my-1"></div>
                                <p class="px-3 pt-2 pb-1 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
                                    Menu HRD / Admin
                                </p>
                                <x-dropdown-link :href="route('admin.gamification.summary', [], false)">
                                    Rekap Poin Kuartal
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.gamification.settings', [], false)">
                                    Pengaturan Gamification
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.policy-log.index', [], false)">
                                    Log Kebijakan Izin
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout', [], false) }}">
                                @csrf

                                <x-dropdown-link :href="route('logout', [], false)"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile avatar + Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                @if ($navUser)
                    @if ($navUser->profile_photo_path)
                        <img src="{{ asset('storage/'.$navUser->profile_photo_path) }}"
                             alt="Profile photo"
                             class="w-8 h-8 rounded-full object-cover border border-white/40">
                    @else
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-700 text-white uppercase text-xs">
                            {{ mb_substr($navUser->name ?? '', 0, 1, 'UTF-8') }}
                        </span>
                    @endif
                @endif

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-emerald-700/40 focus:outline-none focus:bg-emerald-700/40 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-brand-dark text-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard', [], false)" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex items-center gap-3">
                @if ($navUser)
                    @if ($navUser->profile_photo_path)
                        <img src="{{ asset('storage/'.$navUser->profile_photo_path) }}"
                             alt="Profile photo"
                             class="w-9 h-9 rounded-full object-cover border border-emerald-200 shadow-sm">
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-700 text-white uppercase text-xs shadow-sm">
                            {{ mb_substr($navUser->name ?? '', 0, 1, 'UTF-8') }}
                        </span>
                    @endif
                @endif
                <div>
                    <div class="font-medium text-base text-white">{{ $navUser?->name }}</div>
                    <div class="font-medium text-sm text-emerald-100">{{ $navUser?->email }}</div>
                    @php($navPointsMobile = (int) ($navUser?->points ?? 100))
                    <div class="mt-1 inline-flex items-center text-[11px] text-emerald-100 border border-emerald-200/70 rounded-full px-2 py-[1px] bg-emerald-800/40">
                        <span class="font-semibold">Poin:</span>
                        <span class="ml-1">{{ max(0, $navPointsMobile) }}/100</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit', [], false)">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('points.index', [], false)">
                    {{ __('Poin Pelanggaran') }}
                </x-responsive-nav-link>

                @if($isAdminLike)
                    <div class="border-t border-emerald-700/60 my-1"></div>
                    <x-responsive-nav-link :href="route('admin.gamification.summary', [], false)">
                        Rekap Poin Kuartal
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.gamification.settings', [], false)">
                        Pengaturan Gamification
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.policy-log.index', [], false)">
                        Log Kebijakan Izin
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout', [], false)"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
