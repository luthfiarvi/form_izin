<form method="POST" action="{{ route('logout', [], false) }}">
    @csrf
    <button type="submit" {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-25 transition ease-in-out duration-150'
    ]) }}>
        {{ $slot ?? __('Log Out') }}
    </button>
</form>
