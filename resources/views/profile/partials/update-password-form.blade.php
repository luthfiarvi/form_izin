<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update', [], false) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <div class="mt-1 flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-current-password" class="px-3 border-l border-gray-200 text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Lihat password">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <div class="mt-1 flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-new-password" class="px-3 border-l border-gray-200 text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Lihat password">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <div class="mt-1 flex items-stretch rounded-md border border-gray-300 bg-white focus-within:border-emerald-600">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false"
                       class="flex-1 rounded-md rounded-e-none border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0" />
                <button type="button" id="toggle-confirm-password" class="px-3 border-l border-gray-200 text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Lihat password">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button id="btn-save-password" type="submit">Ganti Password</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    @push('scripts')
    <script>
    (function(){
      function setupToggle(inputId, btnId){
        var input = document.getElementById(inputId);
        var btn = document.getElementById(btnId);
        if(!input || !btn) return;
        function set(type){ try{ input.setAttribute('type', type); }catch(e){} }
        btn.addEventListener('click', function(){ set(input.type==='password'?'text':'password'); });
        btn.addEventListener('pointerdown', function(){ set('text'); });
        btn.addEventListener('pointerup', function(){ set('password'); });
        btn.addEventListener('pointerleave', function(){ set('password'); });
      }
      setupToggle('update_password_current_password','toggle-current-password');
      setupToggle('update_password_password','toggle-new-password');
      setupToggle('update_password_password_confirmation','toggle-confirm-password');

      // Ensure Save always submits this form (guard against stray overlays)
      var btn = document.getElementById('btn-save-password');
      if (btn) {
        btn.addEventListener('click', function(e){
          try {
            e.stopPropagation();
            var form = btn.closest('form');
            if (form) form.requestSubmit ? form.requestSubmit() : form.submit();
          } catch (_) {}
        });
      }
    })();
    </script>
    @endpush
</section>
