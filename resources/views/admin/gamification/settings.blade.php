<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Pengaturan Gamification') }}
        </h2>
    </x-slot>

    <div class="py-8 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-sm">
                    @if (session('status'))
                        <div class="mb-4 px-4 py-2 rounded border border-emerald-200 bg-emerald-50 text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-4 text-gray-700">
                        Atur parameter gamification kedisiplinan yang digunakan untuk menghitung pengurangan poin dan blokir pengajuan izin.
                    </p>

                    <form method="post" action="{{ route('admin.gamification.settings.update', [], false) }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Penalti dasar izin non-sakit</label>
                                <input type="number" name="base_penalty_non_sick" min="0" max="100"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('base_penalty_non_sick', $config['base_penalty_non_sick']) }}">
                                <p class="mt-1 text-xs text-gray-500">Contoh: izin pribadi mengurangi sekian poin.</p>
                                <x-input-error :messages="$errors->get('base_penalty_non_sick')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Toleransi menit awal</label>
                                <input type="number" name="tolerance_minutes" min="0" max="480"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('tolerance_minutes', $config['tolerance_minutes']) }}">
                                <p class="mt-1 text-xs text-gray-500">Menit pertama tanpa penalti (misal 60 menit).</p>
                                <x-input-error :messages="$errors->get('tolerance_minutes')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Interval menit setelah toleransi</label>
                                <input type="number" name="interval_minutes" min="1" max="240"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('interval_minutes', $config['interval_minutes']) }}">
                                <p class="mt-1 text-xs text-gray-500">Misal setiap 30 menit.</p>
                                <x-input-error :messages="$errors->get('interval_minutes')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Penalti per interval</label>
                                <input type="number" name="penalty_per_interval" min="0" max="50"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('penalty_per_interval', $config['penalty_per_interval']) }}">
                                <p class="mt-1 text-xs text-gray-500">Poin yang dikurangi per interval (misal 2 poin).</p>
                                <x-input-error :messages="$errors->get('penalty_per_interval')" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Blokir jika points ≤</label>
                                <input type="number" name="block_points_at_or_below" min="0" max="1000"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('block_points_at_or_below', $config['block_points_at_or_below']) }}">
                                <p class="mt-1 text-xs text-gray-500">Biasanya 0 (habis kuota pelanggaran).</p>
                                <x-input-error :messages="$errors->get('block_points_at_or_below')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Blokir jika skor disiplin ≤</label>
                                <input type="number" name="block_discipline_at_or_below" min="0" max="100"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('block_discipline_at_or_below', $config['block_discipline_at_or_below']) }}">
                                <p class="mt-1 text-xs text-gray-500">Biasanya 50 (Zona Bahaya).</p>
                                <x-input-error :messages="$errors->get('block_discipline_at_or_below')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-primary-button>Simpan Pengaturan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

