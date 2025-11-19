<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'btn text-xs uppercase tracking-widest',
]) }}>
    {{ $slot }}
</button>
