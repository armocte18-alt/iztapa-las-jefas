@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg bg-[#0F8A72] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#0C6F5B] transition disabled:opacity-60']) }}>
    {{ $slot }}
</button>
