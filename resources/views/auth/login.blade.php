<x-guest-layout>
    <!-- Session Status Banner -->
    <x-auth-session-status :status="session('status')" />

    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mx-auto h-16 w-16 rounded-lg"
             onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22 viewBox=%220 0 24 24%22%3E%3Crect width=%2224%22 height=%2224%22 rx=%224%22 fill=%22%2310b981%22/%3E%3Ctext x=%2212%22 y=%2216%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22white%22%3ELOGO%3C/text%3E%3C/svg%3E';">
        <div class="mt-3 text-lg sm:text-xl font-bold text-emerald-700 tracking-wider uppercase">IHBS Izin</div>
        <h1 class="mt-3 text-2xl font-semibold text-brand-dark">Login</h1>
        <p class="text-sm text-gray-600 italic">"Hendaklah kalian berkata jujur, karena kejujuran membawa kepada kebaikan." (HR. Tirmidzi)</p>
    </div>

    <form method="POST" action="{{ route('login', [], false) }}" x-data="{ show:false }" class="space-y-3">
        @csrf

        <!-- Email -->
        <label for="email" class="sr-only">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
               placeholder="Email"
               class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 focus:border-emerald-600 focus:ring focus:ring-emerald-200" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="sr-only">Password</label>
            <div class="flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input type="password" x-bind:type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       placeholder="Password"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-password-btn"
                        @click="show = !show"
                        @pointerdown="show = true"
                        @pointerup="show = false"
                        @pointerleave="show = false"
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

        @push('scripts')
        <script>
        (function(){
          var input = document.getElementById('password');
          var btn = document.getElementById('toggle-password-btn');
          if (!input || !btn) return;
          function set(type){ try { input.setAttribute('type', type); } catch(e) {} }
          btn.addEventListener('click', function(){ set(input.type === 'password' ? 'text' : 'password'); });
          btn.addEventListener('pointerdown', function(){ set('text'); });
          btn.addEventListener('pointerup', function(){ set('password'); });
          btn.addEventListener('pointerleave', function(){ set('password'); });
        })();
        </script>
        @endpush

        <div class="mt-2 flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-500">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request', [], false) }}" class="text-sm text-emerald-700 hover:underline">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="mt-6 w-full rounded-md bg-[color:var(--brand-green)] text-white py-2 font-medium hover:bg-emerald-900">Login</button>

        <p class="mt-3 text-center text-sm text-emerald-700">
            Belum punya akun? <a href="{{ route('register', [], false) }}" class="hover:underline">Daftar</a>
        </p>
    </form>
</x-guest-layout>
