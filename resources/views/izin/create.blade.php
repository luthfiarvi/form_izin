<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white leading-tight tracking-tight">
            📋 Pengajuan Form Izin
        </h2>
    </x-slot>

    <div class="py-6 pb-28">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="alert alert-error mb-5" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <div style="font-weight:700;margin-bottom:4px;">Pengajuan belum bisa dikirim</div>
                        <ul style="margin:0;padding-left:16px;list-style:disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Main Form Card --}}
            <div class="card" style="border-radius:20px;overflow:hidden;">

                {{-- Card Header --}}
                <div class="card-header" style="padding:24px 28px 20px;">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(255,255,255,0.15);">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height:36px;width:36px;object-fit:contain;border-radius:8px;"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:28px;height:28px;display:none;color:rgba(255,255,255,0.85);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:1rem;font-weight:700;color:white;margin:0;line-height:1.3;">YAYASAN DAKWAH ISLAM CAHAYA ILMU</h3>
                            <p style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin:4px 0 0;line-height:1.5;">
                                Jl. Mushola Fathul Ulum No.11 Munjul, Cipayung JAK-TIM<br>
                                Telp. 021-84312279 &bull; www.smpihbs.sch.id
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Form Body --}}
                <div style="padding:28px;">
                    <form method="POST" action="{{ route('izin.store', [], false) }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Row 1: Tanggal & Nama --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label class="form-label" for="date">📅 Hari / Tanggal</label>
                                <input type="date" id="date" name="date"
                                       value="{{ old('date', now()->toDateString()) }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label class="form-label" for="name">👤 Nama Pegawai</label>
                                <input type="text" id="name" value="{{ Auth::user()->name }}"
                                       class="form-input"
                                       style="background:#f8fafc;color:#64748b;cursor:not-allowed;"
                                       readonly>
                            </div>
                        </div>

                        {{-- Row 2: Jam Masuk & Keluar --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div>
                                <label class="form-label" for="in_time">🕐 Masuk Jam</label>
                                <input type="time" id="in_time" name="in_time"
                                       value="{{ old('in_time') }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label class="form-label" for="out_time">🕐 Keluar Jam</label>
                                <input type="time" id="out_time" name="out_time"
                                       value="{{ old('out_time') }}"
                                       class="form-input">
                            </div>
                        </div>

                        {{-- Keperluan --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label" for="purpose">📝 Keperluan Izin</label>
                            <textarea id="purpose" name="purpose" rows="3"
                                      class="form-input"
                                      style="resize:vertical;min-height:88px;"
                                      placeholder="Tuliskan keperluan izin secara jelas...">{{ old('purpose') }}</textarea>
                        </div>

                        {{-- Jenis Izin --}}
                        <div style="margin-bottom:16px;">
                            <label class="form-label" for="izin_type">📌 Jenis Izin</label>
                            <select id="izin_type" name="izin_type" class="form-input" style="cursor:pointer;">
                                <option value="">— Pilih Jenis Izin —</option>
                                @foreach (['Sakit','Dinas Luar','Pribadi'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('izin_type') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Lampiran --}}
                        <div style="margin-bottom:24px;">
                            <label class="form-label" for="attachment">📎 Lampiran Bukti</label>
                            <input class="form-input" type="file" name="attachment" id="attachment"
                                   accept=".pdf,image/*"
                                   style="padding:8px;cursor:pointer;">
                            <p style="font-size:0.75rem;color:#94a3b8;margin-top:6px;">
                                PDF / JPG / PNG — maksimal 5MB. Wajib untuk izin Sakit dan Dinas Luar.
                            </p>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-1.5" />
                        </div>

                        {{-- Divider --}}
                        <div style="height:1px;background:linear-gradient(90deg,transparent,#e2e8f0,transparent);margin-bottom:24px;"></div>

                        {{-- Signature Row --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">
                            {{-- Kepala Kepegawaian --}}
                            <div style="text-align:center;">
                                <p style="font-size:0.8125rem;font-weight:600;color:#475569;margin-bottom:8px;">Kepala Kepegawaian</p>
                                <div style="height:90px;border:1.5px dashed #cbd5e1;border-radius:10px;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                                    <span style="font-size:0.75rem;color:#94a3b8;font-style:italic;">Tanda Tangan</span>
                                </div>
                                <div style="height:1px;background:#cbd5e1;margin:10px 20px 0;"></div>
                            </div>

                            {{-- Yang Bersangkutan --}}
                            <div style="text-align:center;">
                                <p style="font-size:0.8125rem;font-weight:600;color:#475569;margin-bottom:8px;">Yang Bersangkutan</p>
                                @php $sig = optional(Auth::user())->signature_path; @endphp
                                @if ($sig)
                                    <div style="height:90px;border-radius:10px;background:white url('{{ asset('storage/'.$sig) }}') no-repeat center/contain;border:1.5px solid #e2e8f0;"></div>
                                @else
                                    <div style="height:90px;border:1.5px dashed #cbd5e1;border-radius:10px;background:#f8fafc;display:flex;align-items:center;justify-content:center;">
                                        <span style="font-size:0.75rem;color:#94a3b8;font-style:italic;">Tanda Tangan</span>
                                    </div>
                                @endif
                                <div style="height:1px;background:#cbd5e1;margin:10px 20px 0;"></div>
                                <p style="margin-top:6px;font-size:0.8125rem;color:#475569;">
                                    ({{ Auth::user()->name }})
                                </p>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div style="display:flex;justify-content:flex-end;gap:12px;">
                            <a href="{{ route('izin.data', [], false) }}" class="btn btn-ghost btn-sm">
                                Lihat Riwayat
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
