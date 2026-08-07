@props(['type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg border border-[var(--border-card)] px-4 py-2.5 text-sm font-semibold text-[var(--text-muted)] hover:bg-[var(--hover-tint)] transition']) }}>
    {{ $slot }}
</button>
