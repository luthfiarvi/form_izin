<section class="space-y-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy', [], false) }}" class="mt-4 space-y-4 max-w-xl">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="delete_password" value="{{ __('Password') }}" />
            <x-text-input
                id="delete_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                placeholder="Masukkan password untuk konfirmasi"
            />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center gap-3">
            <x-danger-button type="submit" onclick="return confirm('Yakin ingin menghapus akun Anda secara permanen?');">
                {{ __('Delete Account') }}
            </x-danger-button>
            <p class="text-xs text-gray-500">Anda akan otomatis logout setelah akun terhapus.</p>
        </div>
    </form>
</section>
