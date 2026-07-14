<x-guest-layout>
    {{-- Header --}}
    <div class="text-center mb-7">
        <div class="auth-logo-ring">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-10 object-contain rounded-full"
                 onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
        <div class="mt-3 text-xs font-bold tracking-widest uppercase" style="color:#059669;letter-spacing:0.12em;">IHBS Izin</div>
        <h1 class="mt-2 text-2xl font-bold" style="color:#0f2d3d;letter-spacing:-0.02em;">Buat Akun Baru</h1>
        <p class="mt-1 text-sm" style="color:#64748b;">Lengkapi formulir di bawah untuk mendaftar</p>
    </div>

    <form method="POST" action="{{ route('register', [], false) }}" enctype="multipart/form-data" x-data="{ show: false }" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="form-label">Nama Lengkap</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </span>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                       placeholder="Nama lengkap Anda" class="form-input" style="padding-left:38px;" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Alamat Email</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                </span>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username"
                       placeholder="nama@contoh.com" class="form-input" style="padding-left:38px;" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- WhatsApp --}}
        <div>
            <label for="whatsapp" class="form-label">No. WhatsApp <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                </span>
                <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}"
                       placeholder="Contoh: 628123456789" class="form-input" style="padding-left:38px;" />
            </div>
        </div>

        {{-- Foto Profil --}}
        <div>
            <label for="profile_photo" class="form-label">Foto Profil <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
            <input id="profile_photo" name="profile_photo" type="file" accept="image/*"
                   class="form-input"
                   style="padding:8px;cursor:pointer;" />
            <p style="font-size:0.72rem;color:#94a3b8;margin-top:4px;">JPG/PNG/HEIC, maksimum 4MB</p>
            <x-input-error :messages="$errors->get('profile_photo')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="form-label">Password</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </span>
                <input type="password" x-bind:type="show ? 'text' : 'password'"
                       id="password" name="password" required autocomplete="new-password"
                       placeholder="Minimal 6 karakter" class="form-input" style="padding-left:38px;padding-right:42px;" />
                <button type="button" @click="show = !show"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;align-items:center;" aria-label="Lihat password">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="form-label">Ulangi Password</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </span>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password"
                       placeholder="Ulangi password" class="form-input" style="padding-left:38px;" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        {{-- Tanda Tangan --}}
        <div>
            <label class="form-label">Tanda Tangan <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
            <div style="border:1.5px solid #e2e8f0;border-radius:12px;padding:14px;background:#fafafa;">
                <canvas id="signature-canvas" width="500" height="140"
                        style="width:100%;border:1px solid #e2e8f0;border-radius:8px;background:white;touch-action:none;display:block;"></canvas>
                <div style="margin-top:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <button type="button" id="clear-signature"
                            style="padding:6px 14px;border-radius:8px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:0.8125rem;font-weight:600;cursor:pointer;">
                        🗑 Bersihkan
                    </button>
                    <label for="signature_file_register"
                           style="padding:6px 14px;border-radius:8px;background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe;font-size:0.8125rem;font-weight:600;cursor:pointer;">
                        📁 Unggah Gambar
                    </label>
                    <span style="font-size:0.75rem;color:#64748b;" data-signature-file-name></span>
                </div>
                <input id="signature_file_register" type="file" name="signature_file" accept="image/*" class="hidden" />
                <input type="hidden" name="signature" id="signature" value="">
                <x-input-error :messages="$errors->get('signature_file')" class="mt-1.5" />
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-full" style="width:100%;padding:12px;font-size:0.9375rem;margin-top:4px;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            Daftar Sekarang
        </button>

        <div style="display:flex;align-items:center;gap:12px;margin-top:2px;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">Sudah punya akun?</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>

        <a href="{{ route('login', [], false) }}"
           class="btn btn-ghost w-full" style="width:100%;padding:11px;font-size:0.875rem;color:#059669;border-color:#d1fae5;background:#f0fdf4;margin-top:-2px;">
            Masuk ke Akun
        </a>
    </form>

    @push('scripts')
    <script>
    (function(){
      const canvas = document.getElementById('signature-canvas');
      if (!canvas || canvas.dataset.sigInitialized === '1' || !canvas.getContext) return;
      const ctx = canvas.getContext('2d');
      let draw = false;
      ctx.strokeStyle = '#1e293b';
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      function pos(e){ const r=canvas.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:(t.clientX-r.left)*(canvas.width/r.width),y:(t.clientY-r.top)*(canvas.height/r.height)}; }
      function start(e){ draw=true; const p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
      function move(e){ if(!draw) return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
      function end(){ draw=false; }
      canvas.addEventListener('pointerdown',start); canvas.addEventListener('pointermove',move); window.addEventListener('pointerup',end);
      canvas.addEventListener('touchstart',start,{passive:false}); canvas.addEventListener('touchmove',move,{passive:false}); window.addEventListener('touchend',end);
      document.getElementById('clear-signature')?.addEventListener('click',()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); });
      const form=canvas.closest('form'); const hidden=document.getElementById('signature');
      if(form&&hidden){ form.addEventListener('submit',()=>{ try{hidden.value=canvas.toDataURL('image/png');}catch(_){} }); }
      const fi=document.getElementById('signature_file_register'); const nameEl=document.querySelector('[data-signature-file-name]');
      if(fi&&nameEl){ fi.addEventListener('change',()=>{ nameEl.textContent=fi.files&&fi.files[0]?fi.files[0].name:''; }); }
      canvas.dataset.sigInitialized='1';
    })();
    </script>
    @endpush
</x-guest-layout>
