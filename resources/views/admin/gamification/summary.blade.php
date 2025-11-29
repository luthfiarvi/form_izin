<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Rekap Poin Per Kuartal') }}
        </h2>
    </x-slot>

    <div class="py-8 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-sm">
                    <form method="get" class="mb-4 flex flex-wrap gap-3 items-end text-sm">
                        <div>
                            <label class="block text-sm mb-1">Tahun</label>
                            <select name="year" class="border rounded p-2">
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Bulan (opsional)</label>
                            <select name="month" class="border rounded p-2">
                                <option value="">Semua</option>
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" @selected($month === $m)>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Tanggal (opsional)</label>
                            <input type="number" name="day" min="1" max="31" class="border rounded p-2 w-24"
                                   value="{{ $day ?: '' }}" placeholder="1-31">
                        </div>
                        <div class="flex-1 min-w-[12rem]">
                            <label class="block text-sm mb-1">Cari nama/email</label>
                            <input type="text" name="q" value="{{ $search }}" class="border rounded p-2 w-full" placeholder="Nama atau email">
                        </div>
                        <div>
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs sm:text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left p-2">Pengguna</th>
                                    <th class="text-left p-2">Tahun</th>
                                    <th class="text-left p-2">Kuartal</th>
                                    <th class="text-left p-2">Poin Awal</th>
                                    <th class="text-left p-2">Poin Akhir (Live)</th>
                                    <th class="text-left p-2">Total Pengurangan (Live)</th>
                                    <th class="text-left p-2">Di-reset Pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    @php
                                        $start = (int) ($row->starting_points ?? 100);
                                        $endStored = (int) ($row->ending_points ?? 0);
                                        $userCurrent = (int) ($row->user?->points ?? $endStored ?: $start);
                                        $liveDeduction = max(0, $start - $userCurrent);
                                        $storedDeduction = (int) ($row->total_deduction ?? 0);
                                        $displayEnd = $userCurrent;
                                        $displayDeduction = max($storedDeduction, $liveDeduction);
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">
                                            {{ $row->user?->name ?? '-' }}
                                            @if($row->user?->email)
                                                <div class="text-[11px] text-gray-500">{{ $row->user->email }}</div>
                                            @endif
                                        </td>
                                        <td class="p-2 whitespace-nowrap">{{ $row->year }}</td>
                                        <td class="p-2 whitespace-nowrap">Q{{ $row->quarter }}</td>
                                        <td class="p-2">{{ $start }}</td>
                                        <td class="p-2">{{ $displayEnd }}</td>
                                        <td class="p-2 text-red-600 font-semibold">-{{ $displayDeduction }}</td>
                                        <td class="p-2 whitespace-nowrap">
                                            {{ optional($row->closed_at)->format('Y-m-d H:i') ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-sm text-gray-500">
                                            Belum ada rekap kuartal untuk tahun ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $rows->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
