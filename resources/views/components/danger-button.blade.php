@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg bg-[#E4572E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#C7461F] transition disabled:opacity-60']) }}>
    {{ $slot }}
</button>
