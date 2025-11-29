<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Pengajuan Form Izin') }}
        </h2>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-2xl border border-emerald-200">
                <div class="p-6 text-gray-900">
                    <div class="text-center mb-6">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mx-auto h-16 w-16" onerror="this.style.display='none'">
                        <h3 class="mt-2 text-2xl font-bold text-emerald-700">YAYASAN DAKWAH ISLAM CAHAYA ILMU</h3>
                        <p class="text-sm text-gray-600">Jl. Mushola Fathul Ulum No.11 Munjul, Cipayung JAK-TIM<br>Telp. 021-84312279 | www.smpihbs.sch.id</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <div class="flex items-start gap-2">
                                <div class="mt-0.5">
                                    <svg class="h-4 w-4 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59C19.02 16.95 18.245 18 17.018 18H2.982C1.755 18 .98 16.95 1.74 14.69l6.517-11.59zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-red-800">Pengajuan belum bisa dikirim</div>
                                    <ul class="mt-1 list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('izin.store', [], false) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1" for="date">Hari/Tanggal</label>
                                <input type="date" id="date" name="date" value="{{ old('date', now()->toDateString()) }}" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="name">Nama</label>
                                <input type="text" id="name" value="{{ Auth::user()->name }}" class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="in_time">Masuk Jam</label>
                                <input type="time" id="in_time" name="in_time" value="{{ old('in_time') }}" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1" for="out_time">Keluar Jam</label>
                                <input type="time" id="out_time" name="out_time" value="{{ old('out_time') }}" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium mb-1" for="purpose">Keperluan</label>
                            <textarea id="purpose" name="purpose" rows="3" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200" placeholder="Tuliskan keperluan izin">{{ old('purpose') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium mb-1" for="izin_type">Jenis Izin</label>
                            <select id="izin_type" name="izin_type" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach (['Sakit','Dinas Luar','Pribadi'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('izin_type')===$opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium mb-1" for="attachment">Lampiran Bukti (PDF/JPG/PNG, maks 5MB)</label>
                            <input class="w-full rounded-md border border-gray-300 bg-white px-3 py-2" type="file" name="attachment" id="attachment" accept=".pdf,image/*">
                            <p class="text-xs text-gray-500 mt-1">Wajib untuk izin Sakit dan Dinas Luar.</p>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-6 items-end">
                            <div class="text-center">
                                <p class="font-semibold">Kepala Kepegawaian</p>
                                <div class="h-24 border rounded bg-gray-50 mt-2 flex items-center justify-center text-gray-400">(Tanda Tangan)</div>
                                <div class="mt-8 border-t mx-8"></div>
                            </div>
                            <div class="text-center">
                                <p class="font-semibold">Yang Bersangkutan</p>
                                @php $sig = optional(Auth::user())->signature_path; @endphp
                                @if ($sig)
                                    <div
                                        class="h-24 rounded bg-white mx-auto bg-no-repeat bg-contain bg-center pointer-events-none select-none"
                                        style="background-image: url('{{ asset('storage/'.$sig) }}');"
                                        aria-label="Signature">
                                    </div>
                                @else
                                    <div class="h-24 border rounded bg-gray-50 mt-2 flex items-center justify-center text-gray-400">(Tanda Tangan)</div>
                                @endif
                                <p class="mt-2 font-semibold">({{ strtolower(Auth::user()->name) }})</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-6 py-2 rounded-md bg-[color:var(--brand-green)] text-white hover:bg-emerald-900">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
