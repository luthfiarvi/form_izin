<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Detail Form Izin #'.$form->id) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900 dark:text-gray-100 space-y-0">
                    <div class="flex items-start justify-between gap-6">
                        <div class="flex-1 space-y-1">
                            <div class="font-semibold text-base">Pemohon</div>
                            <div class="text-sm">
                                {{ $form->user?->name }}
                                <span class="text-gray-500">({{ $form->user?->email }})</span>
                            </div>
                            <div class="mt-1 h-px bg-gray-300 w-full"></div>
                        </div>
                        <div class="flex flex-col items-center">
                            @php $profileUser = $form->user; @endphp
                            <div class="overflow-hidden bg-gray-200 flex items-center justify-center text-gray-600 text-sm uppercase rounded-lg"
                                 style="width: 80px; height: 100px;">
                                @if ($profileUser && $profileUser->profile_photo_path)
                                    <img src="{{ asset('storage/'.$profileUser->profile_photo_path) }}" alt="Foto profil {{ $profileUser->name }}" class="w-full h-full object-cover">
                                @elseif($profileUser)
                                    {{ mb_substr($profileUser->name, 0, 1, 'UTF-8') }}
                                @else
                                    ?
                                @endif
                            </div>
                            <span class="mt-1 text-[11px] text-gray-500">Foto Profil</span>
                        </div>
                    </div>

                    <div class="mt-0 space-y-1">
                        <div class="font-semibold">Alasan Izin</div>
                        <div class="text-sm">
                            <div><span class="font-medium">Jenis:</span> {{ $form->izin_type ?? '-' }}</div>
                            <div class="mt-1"><span class="font-medium">Keperluan:</span> {{ $form->purpose ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-1 space-y-1">
                        <div class="font-semibold">Lampiran</div>
                        @if ($form->attachment_path)
                            @php $fn = basename($form->attachment_path); @endphp
                            <a class="text-indigo-600" href="{{ route('files.attachment', ['filename' => $fn], false) }}" target="_blank">Lihat Lampiran</a>
                        @else
                            <div class="text-gray-500 text-sm">Tidak ada</div>
                        @endif
                    </div>

                    <div class="mt-1 border-t border-gray-300 pt-2 space-y-1">
                        <div class="font-semibold">Status</div>
                        <div class="text-sm">
                            @if ($form->approved_at)
                                <span class="text-green-600">Approved</span> oleh {{ $form->decidedBy?->name }} pada {{ $form->approved_at }}
                            @elseif ($form->rejected_at)
                                <span class="text-red-600">Rejected</span> oleh {{ $form->decidedBy?->name }} pada {{ $form->rejected_at }}
                            @else
                                <span class="text-yellow-600">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-1 flex gap-3">
                        <form method="post" action="{{ route('admin.izin.update', ['formIzin' => $form], false) }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="action" value="approve">
                            <x-primary-button>Approve</x-primary-button>
                        </form>
                        <form method="post" action="{{ route('admin.izin.update', ['formIzin' => $form], false) }}" class="confirm-form" data-confirm="Tolak pengajuan izin ini?">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="action" value="reject">
                            <x-secondary-button type="submit">Reject</x-secondary-button>
                        </form>
                        <form method="post" action="{{ route('admin.izin.destroy', ['formIzin' => $form], false) }}" class="confirm-form" data-confirm="Hapus form ini? Data tidak bisa dipulihkan.">
                            @csrf
                            @method('delete')
                            <button class="px-4 py-2 rounded bg-gray-200 text-gray-800">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi reuse (ikut global) --}}
    <div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="bg-white/90 border border-red-100 rounded-2xl shadow-2xl max-w-md w-full p-6 animate-[fadeIn_0.15s_ease-out]">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M3.84 18h16.32c1.1 0 1.77-1.18 1.23-2.14L13.23 4.86c-.55-.96-1.91-.96-2.46 0L2.61 15.86c-.54.96.14 2.14 1.23 2.14Z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Yakin ingin melanjutkan?</h3>
                    <p id="confirm-message" class="mt-1 text-sm text-gray-700 leading-relaxed">Tindakan ini tidak bisa dibatalkan.</p>
                </div>
            </div>
            <div class="mt-5 flex flex-col sm:flex-row sm:justify-end gap-3">
                <button type="button" id="confirm-cancel" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition">Batal</button>
                <button type="button" id="confirm-yes" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition">Ya, lanjut</button>
            </div>
        </div>
    </div>
</x-app-layout>
