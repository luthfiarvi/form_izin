<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Form Izin</h2>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl border border-emerald-100 border-t-4 border-brand-accent overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <img src="{{ asset('img/logo.png') }}" alt="logo" class="h-14 w-14 mx-auto" onerror="this.style.display='none'">
                        <h3 class="mt-2 text-xl font-extrabold text-brand-accent">YAYASAN DAKWAH ISLAM CAHAYA ILMU</h3>
                        <p class="text-xs text-gray-600">Jl. Mushola Fathul Ulum No.11 Munjul, Cipayung JAK-TIM<br>Telp. 021-84312279 | www.smpihbs.sch.id</p>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Tanggal</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ optional($form->date)->format('Y-m-d') ?: $form->created_at->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Nama</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ optional($form->user)->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Masuk Jam</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ $form->in_time ?? '--:--' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Keluar Jam</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ $form->out_time ?? '--:--' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Jenis Izin</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ $form->izin_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Lampiran</dt>
                            <dd class="font-medium mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">
                                @if($form->attachment_path)
                                    <a class="text-blue-600 hover:underline" target="_blank" href="{{ route('files.attachment', ['filename' => basename($form->attachment_path)], false) }}">Lihat</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        <dt class="text-sm text-gray-500">Keperluan</dt>
                        <dd class="font-medium whitespace-pre-line mt-1 rounded-md border border-gray-200 bg-white px-3 py-2">{{ $form->purpose }}</dd>
                    </div>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                        <div class="text-center">
                            <p class="font-semibold">Kepala Kepegawaian</p>
                            @if($form->head_signature_path)
                                <img src="{{ route('files.head_signature', $form, false) }}" alt="TTD Kepala" class="h-24 rounded bg-white mx-auto mt-2">
                            @else
                                <div class="h-24 border rounded bg-gray-50 mt-2 flex items-center justify-center text-gray-400">(Tanda Tangan)</div>
                            @endif
                            <div class="mt-8 border-t mx-8"></div>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold">Yang Bersangkutan</p>
                            @php $sig = optional($form->user)->signature_path; @endphp
                            @if($sig)
                                <img src="{{ route('files.signature', ['filename' => basename($sig)], false) }}" alt="Signature" class="h-24 rounded bg-white mx-auto">
                            @else
                                <div class="h-24 border rounded bg-gray-50 mt-2 flex items-center justify-center text-gray-400">(Tanda Tangan)</div>
                            @endif
                            <p class="mt-2 font-semibold">({{ optional($form->user)->name }})</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 print:hidden">
                        <button onclick="window.print()" class="btn">Cetak</button>
                        <a href="{{ route('izin.data', [], false) }}" class="btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
