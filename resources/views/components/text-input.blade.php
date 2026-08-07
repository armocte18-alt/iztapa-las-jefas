@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border-[var(--border-card)] text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72] disabled:opacity-60']) }}>
