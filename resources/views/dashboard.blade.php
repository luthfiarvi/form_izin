<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $user = auth()->user();
                $score = (int) ($user->discipline_score ?? 100);
                $status = $user->discipline_status ?? [];
                $label = $status['label'] ?? 'Zona Aman (Role Model)';
                $description = $status['description'] ?? 'Skor kedisiplinan Anda sedang dipantau berdasarkan riwayat pengajuan izin.';
                $badgeClass = $status['badge_class'] ?? 'bg-emerald-100 text-emerald-800 border border-emerald-200';
                $colorKey = $status['color'] ?? 'success';
                $barClass = $colorKey === 'danger'
                    ? 'bg-red-500'
                    : ($colorKey === 'warning' ? 'bg-yellow-400' : 'bg-emerald-500');
                $safeScore = max(0, min(100, $score));
            @endphp

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-emerald-100 mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                                Status Kedisiplinan
                            </h3>
                            <div class="mt-1 flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-gray-900">{{ $safeScore }}</span>
                                <span class="text-sm text-gray-500">/ 100</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                            <p class="text-xs text-gray-600 max-w-xs text-left sm:text-right">
                                {{ $description }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-2 {{ $barClass }} rounded-full" style="width: {{ $safeScore }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
