@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-[var(--text-muted)] mb-1']) }}>
    {{ $value ?? $slot }}
</label>
