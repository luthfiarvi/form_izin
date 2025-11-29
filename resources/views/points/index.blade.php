<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Poin Pelanggaran') }}
        </h2>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-emerald-200 mb-4">
                <div class="p-4 flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Poin Anda saat ini</div>
                        <div class="text-2xl font-bold text-emerald-700">
                            {{ $currentPoints }} <span class="text-base text-gray-500">/ 100</span>
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 text-right">
                        <div class="font-semibold text-gray-700">Tahun penilaian</div>
                        <div class="text-sm text-gray-900">{{ $year }}</div>
                        <div class="mt-1 text-red-600 font-semibold">Total pengurangan: -{{ $totalDeduction }} poin</div>
                    </div>
                </div>

                @if($quarterSummaries->count())
                    <div class="px-4 pb-4">
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-gray-700">
                            @foreach($quarterSummaries as $q)
                                <div class="border border-emerald-100 rounded-lg px-3 py-2 bg-emerald-50">
                                    <div class="font-semibold text-emerald-800">Kuartal {{ $q->quarter }}</div>
                                    <div class="mt-1">
                                        <span class="font-medium">Poin akhir:</span>
                                        <span class="ml-1 font-semibold">{{ $q->ending_points }}/100</span>
                                    </div>
                                    <div class="mt-1 text-red-600">
                                        <span class="font-medium">Pengurangan:</span>
                                        <span class="ml-1">-{{ $q->total_deduction }} poin</span>
                                    </div>
                                    <div class="mt-1 text-[10px] text-gray-500">
                                        Di-reset: {{ optional($q->closed_at)->format('d/m/Y') ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Riwayat Pengurangan Poin</h3>
                    <p class="mt-1 text-xs text-gray-600">
                        Setiap persetujuan izin yang bukan sakit dan/atau berdurasi lebih dari 3 jam akan mengurangi poin Anda.
                    </p>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($forms as $row)
                        @php
                            $total = (int) ($row->points_total_deduction ?? 0);
                            $base = (int) ($row->points_base_deduction ?? 0);
                            $extra = (int) ($row->points_duration_deduction ?? 0);
                            $minutes = $row->points_duration_minutes;
                        @endphp
                        @if($total > 0)
                            <div class="px-4 py-3 text-sm text-gray-800">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900">
                                            {{ optional($row->date)->format('d/m/Y') ?: (is_string($row->date) ? $row->date : optional($row->created_at)->format('d/m/Y')) }}
                                        </div>
                                        <div class="text-[13px] text-gray-800 mt-0.5">
                                            <span class="font-medium">Jenis:</span>
                                            <span class="ml-1">{{ $row->izin_type ?? '-' }}</span>
                                        </div>
                                        @if($row->purpose)
                                            <div class="text-[13px] text-gray-700 mt-0.5">
                                                <span class="font-medium">Keperluan:</span>
                                                <span class="ml-1">{{ \Illuminate\Support\Str::limit($row->purpose, 80) }}</span>
                                            </div>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-600">
                                            <span class="font-medium">Durasi:</span>
                                            @if(!is_null($minutes))
                                                @php($jam = intdiv($minutes, 60))
                                                @php($sisaMenit = $minutes % 60)
                                                <span class="ml-1">
                                                    {{ $jam }} jam {{ $sisaMenit }} menit
                                                    ({{ $row->in_time ?? '--:--' }} - {{ $row->out_time ?? '--:--' }})
                                                </span>
                                            @else
                                                <span class="ml-1">-</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 text-xs text-gray-700">
                                            <span class="font-medium">Keterangan:</span>
                                            @if($base > 0)
                                                <span class="ml-1">Izin {{ strtolower($row->izin_type ?? '') }}: -{{ $base }} poin.</span>
                                            @else
                                                <span class="ml-1">Izin sakit: -0 poin.</span>
                                            @endif
                                            @if($extra > 0)
                                                <span class="ml-1">Durasi lebih dari 3 jam: -{{ $extra }} poin.</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right text-sm font-semibold text-red-600 whitespace-nowrap">
                                        -{{ $total }} pts
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada pengurangan poin.
                        </div>
                    @endforelse
                </div>

                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $forms->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
