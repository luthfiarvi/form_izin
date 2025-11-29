<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Log Kebijakan Perizinan') }}
        </h2>
    </x-slot>

    <div class="py-8 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="get" class="mb-4 flex flex-wrap gap-3 items-end text-sm">
                        <div class="flex-1 min-w-[10rem]">
                            <label class="block text-sm mb-1">Cari Pengguna</label>
                            <input type="text" name="q" value="{{ $search }}" class="border rounded p-2 w-full" placeholder="Nama atau Email">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select name="allowed" class="border rounded p-2">
                                <option value="">Semua</option>
                                <option value="yes" @selected($allowedFilter==='yes')>Diizinkan</option>
                                <option value="no" @selected($allowedFilter==='no')>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left p-2">Waktu</th>
                                    <th class="text-left p-2">Pengguna</th>
                                    <th class="text-left p-2">Kebijakan</th>
                                    <th class="text-left p-2">Hasil</th>
                                    <th class="text-left p-2">Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $reasons = (array) ($log->reasons ?? []);
                                        $firstReason = $reasons[0] ?? '-';
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 whitespace-nowrap">
                                            {{ optional($log->evaluated_at)->format('Y-m-d H:i:s') ?? $log->created_at }}
                                        </td>
                                        <td class="p-2">
                                            {{ $log->user?->name ?? '-' }}
                                            @if($log->user?->email)
                                                <div class="text-[11px] text-gray-500">{{ $log->user->email }}</div>
                                            @endif
                                        </td>
                                        <td class="p-2">{{ $log->policy_key ?? '-' }}</td>
                                        <td class="p-2">
                                            @if($log->allowed)
                                                <span class="inline-flex items-center px-2 py-[1px] rounded-full text-[11px] bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Diizinkan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-[1px] rounded-full text-[11px] bg-red-100 text-red-700 border border-red-200">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-2 text-[11px] sm:text-xs text-gray-700">
                                            {{ $firstReason }}
                                            @if(count($reasons) > 1)
                                                <details class="mt-1">
                                                    <summary class="cursor-pointer text-emerald-700">Lihat semua</summary>
                                                    <ul class="list-disc list-inside mt-1">
                                                        @foreach($reasons as $reason)
                                                            <li>{{ $reason }}</li>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-sm text-gray-500">
                                            Belum ada log kebijakan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

