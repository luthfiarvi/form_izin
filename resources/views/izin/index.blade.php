<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white leading-tight tracking-tight">
            📊 Data Izin Pegawai
        </h2>
    </x-slot>

    <div class="py-6 pb-28">
        <div class="w-full max-w-7xl mx-auto px-3 sm:px-5 lg:px-6">

            {{-- Flash Notifications --}}
            @php($note = session('msg') ?? request()->string('msg')->toString())
            @php($pesan = session('pesan') ?? request()->string('pesan')->toString())
            @if($note)
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $note }}</span>
                </div>
            @elseif($pesan === 'hapus_sukses')
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Data izin berhasil dihapus.</span>
                </div>
            @endif

            {{-- Filter Card --}}
            <div class="card mb-5" style="border-radius:16px;padding:20px 24px;">
                <form method="GET" style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;">
                    <div>
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="from" value="{{ $from ?? '' }}" class="form-input" style="width:160px;">
                    </div>
                    <div>
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="to" value="{{ $to ?? '' }}" class="form-input" style="width:160px;">
                    </div>
                    <div>
                        <label class="form-label">Jenis Izin</label>
                        <select name="jenis_izin" class="form-input" style="width:150px;cursor:pointer;">
                            @php($opts=['' => 'Semua','Sakit' => 'Sakit','Dinas Luar' => 'Dinas Luar','Pribadi' => 'Pribadi'])
                            @foreach($opts as $k => $v)
                                <option value="{{ $k }}" @selected($jenis === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        Filter
                    </button>
                    @if(!empty($isAdmin) && $isAdmin)
                        <a href="{{ route('izin.data.export', ['from' => $from ?? '', 'to' => $to ?? '', 'jenis_izin' => $jenis], false) }}"
                           class="btn btn-sm"
                           style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;box-shadow:0 2px 8px rgba(59,130,246,0.30);margin-left:auto;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Export CSV
                        </a>
                    @endif
                </form>
            </div>

            {{-- =================== MOBILE CARDS =================== --}}
            <div class="sm:hidden space-y-3">
                @php($no = ($forms->currentPage()-1)*$forms->perPage()+1)
                @forelse($forms as $row)
                    @php($status = $row->approved_at ? 'approved' : ($row->rejected_at ? 'rejected' : 'pending'))
                    <div class="izin-card-mobile {{ $status }}">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <span style="font-size:0.8125rem;font-weight:700;color:#0f172a;">
                                        {{ optional($row->date)->format('d/m/Y') ?: (is_string($row->date) ? $row->date : $row->created_at->format('d/m/Y')) }}
                                    </span>
                                    <span class="badge badge-{{ $status }}">
                                        @if($status==='approved') ✓ Disetujui
                                        @elseif($status==='rejected') ✗ Ditolak
                                        @else ⏳ Pending @endif
                                    </span>
                                </div>
                                <div style="font-size:0.8125rem;color:#475569;font-weight:500;">
                                    {{ optional($row->user)->name }}
                                    <span style="color:#94a3b8;font-weight:400;font-size:0.75rem;">#{{ $no++ }}</span>
                                </div>
                                @if($row->purpose)
                                    <div style="margin-top:4px;font-size:0.75rem;color:#64748b;">
                                        {{ \Illuminate\Support\Str::limit($row->purpose, 70) }}
                                    </div>
                                @endif
                            </div>
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-size:0.75rem;font-weight:600;color:#334155;">
                                    {{ $row->in_time ?? '--:--' }} — {{ $row->out_time ?? '--:--' }}
                                </div>
                                <div style="font-size:0.75rem;color:#64748b;margin-top:2px;">{{ $row->izin_type }}</div>
                            </div>
                        </div>

                        <div style="margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;gap:8px;">
                            @php($att = $row->attachment_path)
                            @if($att)
                                @php($viewerUrl = route('files.attachment', ['filename' => basename($att)], false))
                                @php($previewUrl = route('files.attachment', ['filename' => basename($att), 'raw' => 1], false))
                                @php($downloadUrl = route('files.attachment', ['filename' => basename($att), 'raw' => 1, 'download' => 1], false))
                                <a class="btn btn-ghost btn-sm js-attachment-preview"
                                   data-url="{{ $previewUrl }}" data-download="{{ $downloadUrl }}"
                                   href="{{ $viewerUrl }}" style="font-size:0.75rem;padding:5px 10px;">
                                    📎 Lampiran
                                </a>
                            @else
                                <span style="font-size:0.75rem;color:#94a3b8;font-style:italic;">Tanpa lampiran</span>
                            @endif

                            <div style="display:flex;align-items:center;gap:6px;">
                                @if(!empty($isAdmin) && $isAdmin)
                                    @if($status==='pending')
                                        <form action="{{ route('izin.approve', ['formIzin' => $row], false) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#10b981,#059669);color:white;padding:5px 10px;font-size:0.75rem;">✓ Setuju</button>
                                        </form>
                                        <form action="{{ route('izin.reject', ['formIzin' => $row], false) }}" method="POST" class="confirm-form" data-confirm="Yakin ingin menolak?" onsubmit="return confirmAction(event, this.dataset.confirm);" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding:5px 10px;font-size:0.75rem;">✗ Tolak</button>
                                        </form>
                                    @else
                                        <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="btn btn-ghost btn-sm" style="font-size:0.75rem;padding:5px 10px;">Detail</a>
                                    @endif
                                @else
                                    @if($status==='approved')
                                        <a href="{{ route('izin.view', ['formIzin' => $row], false) }}" class="btn btn-ghost btn-sm" style="font-size:0.75rem;padding:5px 10px;">Lihat Form</a>
                                    @else
                                        <span style="font-size:0.75rem;color:#94a3b8;font-style:italic;">Menunggu...</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="background:white;border-radius:14px;padding:40px 24px;text-align:center;border:1.5px dashed #e2e8f0;">
                        <div style="font-size:2.5rem;margin-bottom:12px;">📭</div>
                        <p style="color:#94a3b8;font-size:0.875rem;">Tidak ada data izin.</p>
                    </div>
                @endforelse
            </div>

            {{-- =================== DESKTOP TABLE =================== --}}
            <div class="hidden sm:block card" style="border-radius:16px;overflow:hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Jenis</th>
                            <th>Lampiran</th>
                            <th>Keperluan</th>
                            <th>Waktu Input</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="background:white;">
                        @php($no = ($forms->currentPage()-1)*$forms->perPage()+1)
                        @forelse($forms as $row)
                            @php($status = $row->approved_at ? 'approved' : ($row->rejected_at ? 'rejected' : 'pending'))
                            <tr>
                                <td style="color:#94a3b8;font-weight:500;">{{ $no++ }}</td>
                                <td style="font-weight:600;white-space:nowrap;">
                                    {{ optional($row->date)->format('d/m/Y') ?: (is_string($row->date) ? $row->date : $row->created_at->format('d/m/Y')) }}
                                </td>
                                <td style="white-space:nowrap;font-weight:500;">{{ optional($row->user)->name }}</td>
                                <td style="white-space:nowrap;color:#475569;">{{ $row->in_time ?? '--:--' }}</td>
                                <td style="white-space:nowrap;color:#475569;">{{ $row->out_time ?? '--:--' }}</td>
                                <td style="white-space:nowrap;">
                                    <span style="background:#f1f5f9;color:#334155;padding:2px 8px;border-radius:6px;font-size:0.75rem;font-weight:500;">
                                        {{ $row->izin_type }}
                                    </span>
                                </td>
                                <td style="white-space:nowrap;">
                                    @php($att = $row->attachment_path)
                                    @if($att)
                                        @php($viewerUrl = route('files.attachment', ['filename' => basename($att)], false))
                                        @php($previewUrl = route('files.attachment', ['filename' => basename($att), 'raw' => 1], false))
                                        @php($downloadUrl = route('files.attachment', ['filename' => basename($att), 'raw' => 1, 'download' => 1], false))
                                        <a class="js-attachment-preview"
                                           data-url="{{ $previewUrl }}" data-download="{{ $downloadUrl }}"
                                           href="{{ $viewerUrl }}"
                                           style="color:#3b82f6;font-weight:500;text-decoration:none;font-size:0.8125rem;"
                                           onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
                                            📎 Lihat
                                        </a>
                                    @else
                                        <span style="color:#cbd5e1;font-style:italic;">—</span>
                                    @endif
                                </td>
                                <td style="max-width:200px;word-break:break-word;color:#475569;">{{ $row->purpose }}</td>
                                <td style="white-space:nowrap;color:#94a3b8;font-size:0.75rem;">{{ optional($row->created_at)->format('d/m/Y H:i') }}</td>
                                <td style="white-space:nowrap;">
                                    <span class="badge badge-{{ $status }}">
                                        @if($status==='approved') ✓ Disetujui
                                        @elseif($status==='rejected') ✗ Ditolak
                                        @else ⏳ Pending @endif
                                    </span>
                                </td>
                                <td style="white-space:nowrap;">
                                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                        @if(!empty($isAdmin) && $isAdmin)
                                            @if($status==='pending')
                                                <form action="{{ route('izin.approve', ['formIzin' => $row], false) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#10b981,#059669);color:white;padding:4px 10px;font-size:0.75rem;">✓</button>
                                                </form>
                                                <form action="{{ route('izin.reject', ['formIzin' => $row], false) }}" method="POST" class="confirm-form" data-confirm="Yakin ingin menolak?" onsubmit="return confirmAction(event, this.dataset.confirm);" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 10px;font-size:0.75rem;">✗</button>
                                                </form>
                                            @else
                                                <a href="{{ route('izin.view', ['formIzin' => $row], false) }}"
                                                   style="color:#3b82f6;font-size:0.8125rem;font-weight:500;text-decoration:none;"
                                                   onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
                                                   Lihat Form
                                                </a>
                                            @endif
                                            <form action="{{ route('izin.delete', ['formIzin' => $row], false) }}" method="POST" class="confirm-form" data-confirm="Yakin ingin menghapus data ini?" onsubmit="return confirmAction(event, this.dataset.confirm);" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:0.8125rem;font-weight:500;padding:0;"
                                                        onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">Hapus</button>
                                            </form>
                                        @else
                                            @if($status==='approved')
                                                <a href="{{ route('izin.view', ['formIzin' => $row], false) }}"
                                                   style="color:#3b82f6;font-size:0.8125rem;font-weight:500;text-decoration:none;"
                                                   onmouseenter="this.style.textDecoration='underline'" onmouseleave="this.style.textDecoration='none'">
                                                   Lihat Form
                                                </a>
                                            @else
                                                <span style="color:#94a3b8;font-style:italic;font-size:0.8125rem;">Menunggu</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align:center;padding:48px 24px;">
                                    <div style="font-size:2.5rem;margin-bottom:10px;">📭</div>
                                    <p style="color:#94a3b8;font-size:0.875rem;">Tidak ada data izin.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-5">
                {{ $forms->withQueryString()->links() }}
            </div>
        </div>
    </div>

</x-app-layout>

{{-- Confirm Modal --}}
<div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);">
    <div style="background:white;border-radius:20px;box-shadow:0 25px 80px rgba(0,0,0,0.25);max-width:420px;width:calc(100%-32px);padding:28px;animation:card-in 0.2s ease both;">
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:#fee2e2;color:#ef4444;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:24px;height:24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>
            <div style="flex:1;">
                <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 6px;">Konfirmasi Tindakan</h3>
                <p id="confirm-message" style="font-size:0.875rem;color:#475569;margin:0;line-height:1.6;">Tindakan ini tidak bisa dibatalkan.</p>
            </div>
        </div>
        <div style="margin-top:22px;display:flex;justify-content:flex-end;gap:10px;">
            <button type="button" id="confirm-cancel" class="btn btn-ghost btn-sm">Batal</button>
            <button type="button" id="confirm-yes" class="btn btn-danger btn-sm">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

{{-- Attachment Preview Modal --}}
<div id="attachment-modal" class="hidden fixed inset-0 z-50 items-center justify-center" style="background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);padding:16px;">
    <div style="background:white;border-radius:20px;box-shadow:0 25px 80px rgba(0,0,0,0.30);width:100%;max-width:900px;height:85vh;display:flex;flex-direction:column;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:0.9375rem;font-weight:700;color:#0f172a;margin:0;">Pratinjau Lampiran</h3>
            <button id="attachment-close" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:1.25rem;line-height:1;padding:4px;" aria-label="Tutup">&times;</button>
        </div>
        <div style="flex:1;background:#f8fafc;overflow:hidden;">
            <iframe id="attachment-frame" style="width:100%;height:100%;border:none;" src="" title="Lampiran"></iframe>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #f1f5f9;">
            <button id="attachment-back" type="button" class="btn btn-ghost btn-sm">Kembali</button>
            <a id="attachment-download" class="btn btn-primary btn-sm" href="#" download>
                <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Unduh
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // Confirm modal
    let modal, msgEl, btnYes, btnCancel, pendingForm = null;
    function ensureModal() {
        if (modal) return true;
        modal = document.getElementById('confirm-modal');
        if (!modal) return false;
        msgEl = document.getElementById('confirm-message');
        btnYes = document.getElementById('confirm-yes');
        btnCancel = document.getElementById('confirm-cancel');
        btnYes.addEventListener('click', function () { if (pendingForm) pendingForm.submit(); closeModal(); });
        btnCancel.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
        return true;
    }
    function openModal(message, form) {
        if (!ensureModal()) return false;
        pendingForm = form;
        msgEl.textContent = message || 'Apakah Anda yakin?';
        modal.classList.remove('hidden'); modal.classList.add('flex');
        return true;
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden'); modal.classList.remove('flex');
        pendingForm = null;
    }
    window.confirmAction = function (event, message) {
        const form = event.target;
        event.preventDefault();
        if (!openModal(message, form)) { if (window.confirm(message)) form.submit(); }
        return false;
    };
})();

// Attachment preview modal
(function () {
    const modal = document.getElementById('attachment-modal');
    const frame = document.getElementById('attachment-frame');
    const closeBtn = document.getElementById('attachment-close');
    const backBtn = document.getElementById('attachment-back');
    const downloadBtn = document.getElementById('attachment-download');
    if (!modal || !frame) return;
    function open(url) { frame.src = url; modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function close() { frame.src = ''; modal.classList.add('hidden'); modal.classList.remove('flex'); }
    closeBtn?.addEventListener('click', close);
    backBtn?.addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    document.querySelectorAll('.js-attachment-preview').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = link.dataset.url || link.href;
            if (downloadBtn) downloadBtn.href = link.dataset.download || url;
            open(url);
        });
    });
})();
</script>
@endpush
