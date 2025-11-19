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
                        <form method="post" action="{{ route('admin.izin.update', ['formIzin' => $form], false) }}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="action" value="reject">
                            <x-secondary-button>Reject</x-secondary-button>
                        </form>
                        <form method="post" action="{{ route('admin.izin.destroy', ['formIzin' => $form], false) }}" onsubmit="return confirm('Hapus form ini?')">
                            @csrf
                            @method('delete')
                            <button class="px-4 py-2 rounded bg-gray-200 text-gray-800">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
