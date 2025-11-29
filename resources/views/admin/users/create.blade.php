<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Tambah Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($errors->any())
                        <div class="mb-4 text-red-600">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('admin.users.store', [], false) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm mb-1">Nama</label>
                            <input type="text" name="name" class="border rounded p-2 w-full" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Email</label>
                            <input type="email" name="email" class="border rounded p-2 w-full" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Password</label>
                            <input type="password" name="password" class="border rounded p-2 w-full" required>
                            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Role</label>
                            <select name="role" class="border rounded p-2" required>
                                <option value="user" @selected(old('role')==='user')>User</option>
                                <option value="admin" @selected(old('role')==='admin')>Admin</option>
                                <option value="hr" @selected(old('role')==='hr')>SDM / HRD</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_kepala_kepegawaian" value="1" @checked(old('is_kepala_kepegawaian'))>
                                <span class="ms-2">Kepala Kepegawaian</span>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Status</label>
                            <input type="text" name="status" class="border rounded p-2 w-full" value="{{ old('status', 'active') }}" placeholder="active/inactive">
                        </div>
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Points (kuota pelanggaran)</label>
                                <input type="number" name="points" min="0" max="1000"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('points', 100) }}">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Skor Kedisiplinan</label>
                                <input type="number" name="discipline_score" min="0" max="100"
                                       class="border rounded p-2 w-full"
                                       value="{{ old('discipline_score', 100) }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">No. WhatsApp</label>
                            <input type="text" name="whatsapp_phone" class="border rounded p-2 w-full" value="{{ old('whatsapp_phone') }}">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Foto Profil</label>
                            <div class="flex items-center gap-4 mt-1">
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-sm uppercase">
                                    {{ old('name') ? mb_substr(old('name'), 0, 1, 'UTF-8') : 'U' }}
                                </div>
                                <input type="file" name="profile_photo" accept="image/*"
                                       class="block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Format: gambar apa pun (JPG/PNG/HEIC, dll), maksimum 4MB.</p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm mb-1">Tanda Tangan (file)</label>
                            <input id="signature_file_admin" type="file" name="signature_file" class="border rounded p-2 w-full" accept="image/*">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm mb-1">Tanda Tangan (gambar via kanvas)</label>
                            <div class="mt-2 border rounded p-3">
                                <canvas data-signature-canvas id="signature-canvas" width="500" height="160" class="w-full border bg-white rounded touch-none"></canvas>
                                <div class="mt-2 flex items-center gap-2">
                                    <button type="button" id="clear-signature" class="px-3 py-1 bg-gray-200 rounded">Bersihkan</button>
                                    <label for="signature_file_admin" class="px-3 py-1 bg-blue-600 text-white rounded cursor-pointer">Unggah</label>
                                    <span class="text-xs text-gray-600" data-signature-file-name></span>
                                </div>
                                <input type="hidden" name="signature" id="signature" value="">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Opsional: gambar tanda tangan bila tidak mengunggah file.</p>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('profile.edit', [], false) }}"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-md border border-red-500 text-red-600 text-xs font-semibold uppercase tracking-widest hover:bg-red-50 hover:text-red-700 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function(){
      const canvas = document.querySelector('canvas#signature-canvas');
      if (!canvas || canvas.dataset.sigInitialized === '1' || !canvas.getContext) return;
      const ctx = canvas.getContext('2d');
      let draw = false;
      function pos(e){ const r=canvas.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; }
      function start(e){ draw=true; const p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
      function move(e){ if(!draw) return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
      function end(){ draw=false; }
      canvas.style.touchAction='none';
      canvas.addEventListener('pointerdown',start); canvas.addEventListener('pointermove',move); window.addEventListener('pointerup',end);
      canvas.addEventListener('touchstart',start,{passive:false}); canvas.addEventListener('touchmove',move,{passive:false}); window.addEventListener('touchend',end);
      const clearBtn=document.getElementById('clear-signature'); if(clearBtn){ clearBtn.addEventListener('click',()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); }); }
      const form=canvas.closest('form'); const hidden=form?form.querySelector('input[name=\"signature\"]'):null; if(form&&hidden){ form.addEventListener('submit',()=>{ try{ hidden.value=canvas.toDataURL('image/png'); }catch(_){} }); }
      const fi=document.getElementById('signature_file_admin'); const nameEl=document.querySelector('[data-signature-file-name]'); if(fi&&nameEl){ fi.addEventListener('change',()=>{ nameEl.textContent=fi.files&&fi.files[0]?fi.files[0].name:''; }); }
      canvas.dataset.sigInitialized='1';
    })();
    </script>
    @endpush
</x-app-layout>
