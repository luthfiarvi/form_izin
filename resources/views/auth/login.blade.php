<x-guest-layout>
    {{-- Session Status Banner --}}
    @if (session('status'))
        <div class="alert alert-success mb-5" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Logo & Heading --}}
    <div class="text-center mb-8">
        <div class="auth-logo-ring">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-10 object-contain rounded-full"
                 onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div class="mt-3 text-xs font-bold tracking-widest uppercase" style="color:#059669;letter-spacing:0.12em;">IHBS Izin</div>
        <h1 class="mt-2 text-2xl font-bold" style="color:#0f2d3d;letter-spacing:-0.02em;">Selamat Datang</h1>
        <p class="mt-1 text-sm" style="color:#64748b;">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login', [], false) }}" x-data="{ show: false }" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Alamat Email</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </span>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       placeholder="nama@contoh.com"
                       class="form-input" style="padding-left:38px;" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="form-label">Password</label>
            <div style="position:relative;">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <input type="password" x-bind:type="show ? 'text' : 'password'"
                       id="password" name="password" required autocomplete="current-password"
                       placeholder="Masukkan password"
                       class="form-input" style="padding-left:38px;padding-right:42px;" />
                <button type="button" id="toggle-password-btn"
                        @click="show = !show"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;background:none;border:none;cursor:pointer;padding:0;display:flex;align-items:center;"
                        aria-label="Lihat password">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Remember & Forgot --}}
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm cursor-pointer" style="color:#475569;">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request', [], false) }}"
                   class="text-sm font-medium hover:underline" style="color:#059669;">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-full mt-2" style="width:100%;padding:12px;font-size:0.9375rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Masuk
        </button>

        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:12px;margin-top:6px;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">Belum punya akun?</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>

        <a href="{{ route('register', [], false) }}"
           class="btn btn-ghost w-full" style="width:100%;padding:11px;font-size:0.875rem;color:#059669;border-color:#d1fae5;background:#f0fdf4;">
            Daftar Akun Baru
        </a>
    </form>

    {{-- Hadits --}}
    <p class="mt-6 text-center text-xs italic" style="color:#94a3b8;line-height:1.6;">
        "Hendaklah kalian berkata jujur, karena kejujuran<br>membawa kepada kebaikan." <span style="font-style:normal;">(HR. Tirmidzi)</span>
    </p>

    @push('scripts')
    <script>
    (function(){
      var btn=document.getElementById('toggle-password-btn');
      var input=document.getElementById('password');
      if(!btn||!input)return;
      function setType(t){try{input.setAttribute('type',t);}catch(e){}}
      btn.addEventListener('click',function(){setType(input.type==='password'?'text':'password');});
    })();
    </script>
    @endpush
</x-guest-layout>
