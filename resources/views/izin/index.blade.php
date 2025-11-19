<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Data Izin Pegawai</h2>
    </x-slot>

    <div class="py-6 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @php($note = session('msg') ?? request()->string('msg')->toString())
            @php($pesan = session('pesan') ?? request()->string('pesan')->toString())
            @if($note)
                <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800">{{ $note }}</div>
            @elseif($pesan==='hapus_sukses')
                <div class="mb-4 px-4 py-2 rounded bg-red-100 text-red-800">Data izin dihapus.</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-4 mb-4">
                <form method="GET" class="mb-3 flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $from ?? '' }}" class="border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $to ?? '' }}" class="border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Jenis Izin</label>
                        <select name="jenis_izin" class="border rounded p-2">
                            @php($opts=['' => 'Semua','Sakit'=>'Sakit','Dinas Luar'=>'Dinas Luar','Pribadi'=>'Pribadi'])
                            @foreach($opts as $k=>$v)
                                <option value="{{ $k }}" @selected($jenis===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-[color:var(--brand-green)] text-white px-4 py-2 rounded hover:bg-emerald-900">Filter</button>

                    @if(!empty($isAdmin) && $isAdmin)
                        <a href="{{ route('izin.data.export', ['from'=>$from ?? '', 'to'=>$to ?? '', 'jenis_izin'=>$jenis], false) }}" class="ml-auto bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Export CSV</a>
                    @endif
                </form>
            </div>

            {{-- Mobile-friendly list (tanpa perlu scroll horizontal) --}}
            <div class="sm:hidden space-y-4">
                @php($no = ($forms->currentPage()-1)*$forms->perPage()+1)
                @forelse($forms as $row)
                    @php($status = $row->approved_at ? 'approved' : ($row->rejected_at ? 'rejected' : 'pending'))
                    <div class="bg-white shadow-md rounded-xl border border-gray-200 p-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <div class="font-semibold text-[13px] text-gray-900">
                                    {{ optional($row->date)->format('d/m/Y') ?: (is_string($row->date) ? $row->date : $row->created_at->format('d/m/Y')) }}
                                </div>
                                <div class="text-gray-700 text-[13px]">
                                    {{ optional($row->user)->name }}
                                    <span class="text-gray-400 text-[11px]">#{{ $no++ }}</span>
                                </div>
                                @if($row->purpose)
                                    <div class="mt-1 text-[11px] text-gray-700">
                                        <span class="font-semibold">Keperluan:</span>
                                        <span>{{ \Illuminate\Support\Str::limit($row->purpose, 60) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right text-[11px] text-gray-600 space-y-0.5">
                                <div class="font-medium">{{ $row->in_time ?? '--:--' }} - {{ $row->out_time ?? '--:--' }}</div>
                                <div class="capitalize">{{ $row->izin_type }}</div>
                                <div>
                                    <span class="font-semibold">Status:</span>
                                    <span class="capitalize
                                        @if($status==='approved') text-green-600
                                        @elseif($status==='rejected') text-red-600
                                        @else text-yellow-600 @endif">
                                        {{ $status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex justify-between items-center border-t border-gray-100 pt-2">
                            @php($att = $row->attachment_path)
                            @if($att)
                                <a class="text-blue-600 hover:underline" target="_blank" href="{{ route('files.attachment', ['filename' => basename($att)], false) }}">Lampiran</a>
                            @else
                                <span class="text-gray-400 italic">Tanpa lampiran</span>
                            @endif

                            <div class="flex items-center gap-2">
                                @if(!empty($isAdmin) && $isAdmin)
                                    @if($status==='pending')
                                        <form action="{{ route('izin.approve', ['formIzin' => $row], false) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded bg-green-600 text-white">Approve</button>
                                        </form>
                                        <form action="{{ route('izin.reject', ['formIzin' => $row], false) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak?')">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded bg-red-600 text-white">Reject</button>
                                        </form>
                                    @else
                                        <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="text-blue-600 hover:underline">Detail</a>
                                    @endif
                                @else
                                    @if($status==='approved')
                                        <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="text-blue-600 hover:underline">Detail</a>
                                    @else
                                        <span class="text-gray-500 italic">Menunggu</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                        Tidak ada data.
                    </div>
                @endforelse
            </div>

            {{-- Tabel penuh untuk layar >= sm (scroll horizontal jika perlu) --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-xl overflow-hidden">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">#</th>
                            <th class="py-3 px-4 text-left">Tanggal</th>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Masuk Jam</th>
                            <th class="py-3 px-4 text-left">Keluar Jam</th>
                            <th class="py-3 px-4 text-left">Jenis Izin</th>
                            <th class="py-3 px-4 text-left">Lampiran</th>
                            <th class="py-3 px-4 text-left">Keperluan</th>
                            <th class="py-3 px-4 text-left">Waktu Input</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($no = ($forms->currentPage()-1)*$forms->perPage()+1)
                        @forelse($forms as $row)
                            @php($status = $row->approved_at ? 'approved' : ($row->rejected_at ? 'rejected' : 'pending'))
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4">{{ $no++ }}</td>
                                <td class="py-2 px-4">{{ optional($row->date)->format('d/m/Y') ?: (is_string($row->date) ? $row->date : $row->created_at->format('d/m/Y')) }}</td>
                                <td class="py-2 px-4">{{ optional($row->user)->name }}</td>
                                <td class="py-2 px-4">{{ $row->in_time ?? '--:--' }}</td>
                                <td class="py-2 px-4">{{ $row->out_time ?? '--:--' }}</td>
                                <td class="py-2 px-4 capitalize">{{ $row->izin_type }}</td>
                                <td class="py-2 px-4">
                                    @php($att = $row->attachment_path)
                                    @if($att)
                                        <a class="text-blue-600 hover:underline" target="_blank" href="{{ route('files.attachment', ['filename' => basename($att)], false) }}">Lihat</a>
                                    @else
                                        <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4">{{ $row->purpose }}</td>
                                <td class="py-2 px-4">{{ optional($row->created_at)->format('Y-m-d H:i:s') }}</td>
                                <td class="py-2 px-4 capitalize">{{ $status }}</td>
                                <td class="py-2 px-4 whitespace-nowrap">
                                    @if(!empty($isAdmin) && $isAdmin)
                                        @if($status==='pending')
                                            <form action="{{ route('izin.approve', ['formIzin' => $row], false) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:underline mr-2">Approve</button>
                                            </form>
                                            <form action="{{ route('izin.reject', ['formIzin' => $row], false) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menolak?')">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:underline mr-2">Reject</button>
                                            </form>
                                        @else
                                            <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="text-blue-600 hover:underline mr-2">Lihat Form</a>
                                        @endif
                                        <form action="{{ route('izin.delete', ['formIzin' => $row], false) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-1">Hapus</button>
                                        </form>
                                    @else
                                        @if($status==='approved')
                                            <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="text-blue-600 hover:underline">Lihat Form</a>
                                        @else
                                            <span class="text-gray-500 italic">Menunggu persetujuan</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="py-4 text-center">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $forms->withQueryString()->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
