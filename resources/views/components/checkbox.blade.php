@props(['disabled' => false])

<input type="checkbox" @disabled($disabled) {{ $attributes->merge(['class' => 'rounded border-[var(--border-card)] text-[#0F8A72] focus:ring-[#0F8A72]']) }}>
