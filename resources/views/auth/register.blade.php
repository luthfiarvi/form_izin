<x-guest-layout>
    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mx-auto h-16 w-16" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22 viewBox=%220 0 24 24%22%3E%3Crect width=%2224%22 height=%2224%22 rx=%224%22 fill=%22%2310b981%22/%3E%3Ctext x=%2212%22 y=%2216%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22white%22%3ELOGO%3C/text%3E%3C/svg%3E';"> 
        <div class="mt-3 text-lg sm:text-xl font-bold text-emerald-700 tracking-wider uppercase">IHBS Izin</div>
        <h1 class="mt-3 text-2xl font-semibold text-brand-dark">Registrasi Akun</h1>
        <p class="text-sm text-gray-500">Buat akun untuk akses sistem</p>
    </div>

    <form method="POST" action="{{ route('register', [], false) }}" enctype="multipart/form-data" x-data="{ show: false }">
        @csrf

        <!-- Username -->
        <label for="name" class="sr-only">Username</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
               placeholder="Username"
               class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />

        <!-- Email (opsional) -->
        <label for="email" class="sr-only">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username"
               placeholder="Email"
               class="mt-4 w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

        <!-- WhatsApp (opsional) -->
        <label for="whatsapp" class="sr-only">WhatsApp</label>
        <input id="whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}"
               placeholder="No. WhatsApp (opsional, contoh 62812xxxx)"
               class="mt-4 w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200" />

        <!-- Foto Profil (opsional) -->
        <div class="mt-4">
            <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (opsional)</label>
            <input id="profile_photo" name="profile_photo" type="file" accept="image/*"
                   class="w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[color:var(--brand-green)] file:text-white hover:file:bg-emerald-900" />
            <p class="mt-1 text-xs text-gray-500">Format: gambar apa pun (JPG/PNG/HEIC, dll), maksimum 4MB.</p>
            <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="sr-only">Password</label>
            <div class="flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input type="password" x-bind:type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       placeholder="Password (min 6 karakter)"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-register-password-btn"
                        @click="show = !show" @pointerdown="show = true" @pointerup="show = false" @pointerleave="show = false"
                        class="px-3 border-l border-gray-200 text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Lihat password" title="Tahan untuk mengintip / klik untuk toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
        <div class="mt-4">
            <div class="flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input id="password_confirmation" name="password_confirmation" type="password" x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       placeholder="Ulangi password"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-register-confirm-btn"
                        @click="show = !show" @pointerdown="show = true" @pointerup="show = false" @pointerleave="show = false"
                        class="px-3 border-l border-gray-200 text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="Lihat password" title="Tahan untuk mengintip / klik untuk toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>
        </div>
        @push('scripts')
        <script>
        (function(){
          var p = document.getElementById('password');
          var pc = document.getElementById('password_confirmation');
          var bp = document.getElementById('toggle-register-password-btn');
          var bc = document.getElementById('toggle-register-confirm-btn');
          function set(el,type){ if(el){ try{ el.setAttribute('type', type);}catch(e){} } }
          if (bp && p){
            bp.addEventListener('click', function(){ set(p, p.type==='password'?'text':'password'); });
            bp.addEventListener('pointerdown', function(){ set(p,'text'); });
            bp.addEventListener('pointerup', function(){ set(p,'password'); });
            bp.addEventListener('pointerleave', function(){ set(p,'password'); });
          }
          if (bc && pc){
            bc.addEventListener('click', function(){ set(pc, pc.type==='password'?'text':'password'); });
            bc.addEventListener('pointerdown', function(){ set(pc,'text'); });
            bc.addEventListener('pointerup', function(){ set(pc,'password'); });
            bc.addEventListener('pointerleave', function(){ set(pc,'password'); });
          }
        })();
        </script>
        @endpush
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

        <!-- Signature (opsional) -->
        <div class="mt-6">
            <p class="mb-2 text-sm text-gray-700">Tanda Tangan (opsional)</p>
            <div class="border rounded-md p-3">
                <canvas id="signature-canvas" width="500" height="160" class="w-full border rounded bg-white touch-none"></canvas>
                <div class="mt-2 flex items-center gap-2">
                    <button type="button" id="clear-signature" class="px-3 py-1 text-sm rounded bg-amber-500 text-white hover:bg-amber-600">Bersihkan</button>
                    <label for="signature_file_register" class="px-3 py-1 text-sm rounded bg-blue-600 text-white hover:bg-blue-700 cursor-pointer">Unggah</label>
                    <span class="text-xs text-gray-600" data-signature-file-name></span>
                </div>
                <input id="signature_file_register" type="file" name="signature_file" accept="image/*" class="hidden" />
                <input type="hidden" name="signature" id="signature" value="">
                <x-input-error :messages="$errors->get('signature_file')" class="mt-2" />
                <x-input-error :messages="$errors->get('signature')" class="mt-1" />
            </div>
        </div>

        @push('scripts')
        <script>
        (function(){
          const canvas=document.getElementById('signature-canvas');
          if(!canvas || canvas.dataset.sigInitialized==='1' || !canvas.getContext) return;
          const ctx=canvas.getContext('2d'); let draw=false;
          function pos(e){ const r=canvas.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; }
          function start(e){ draw=true; const p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
          function move(e){ if(!draw) return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
          function end(){ draw=false; }
          canvas.style.touchAction='none';
          canvas.addEventListener('pointerdown',start); canvas.addEventListener('pointermove',move); window.addEventListener('pointerup',end);
          canvas.addEventListener('touchstart',start,{passive:false}); canvas.addEventListener('touchmove',move,{passive:false}); window.addEventListener('touchend',end);
          const clearBtn=document.getElementById('clear-signature'); if(clearBtn){ clearBtn.addEventListener('click',()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); }); }
          const form=canvas.closest('form'); const hidden=document.getElementById('signature'); if(form&&hidden){ form.addEventListener('submit',()=>{ try{ hidden.value=canvas.toDataURL('image/png'); }catch(_){} }); }
          const fi=document.getElementById('signature_file_register'); const nameEl=document.querySelector('[data-signature-file-name]'); if(fi&&nameEl){ fi.addEventListener('change',()=>{ nameEl.textContent=fi.files&&fi.files[0]?fi.files[0].name:''; }); }
          canvas.dataset.sigInitialized='1';
        })();
        </script>
        @endpush

        <button type="submit" class="mt-6 w-full rounded-md bg-[color:var(--brand-green)] text-white py-2 font-medium hover:bg-emerald-900">Daftar</button>

        <p class="mt-3 text-center text-sm text-emerald-700">
            Sudah punya akun? <a href="{{ route('login', [], false) }}" class="hover:underline">Login</a>
        </p>
    </form>
</x-guest-layout>
