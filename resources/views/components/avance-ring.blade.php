{{--
    Pieza central del dashboard: anillo de progreso mostrando el avance real
    de impresión (impresos vs total). Es la única "hero visual" de la
    página — el resto se mantiene deliberadamente sobrio.
--}}
@props(['total', 'impresos'])

@php
    $porcentaje = $total > 0 ? round(($impresos / $total) * 100) : 0;
    $radio = 54;
    $circunferencia = 2 * M_PI * $radio;
    $offset = $circunferencia - ($circunferencia * $porcentaje / 100);
    $pendientes = $total - $impresos;
@endphp

<div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-6 flex items-center gap-6 flex-wrap sm:flex-nowrap" style="border-top-color: #0F8A72">
    <div class="relative shrink-0 h-32 w-32">
        <svg viewBox="0 0 120 120" class="h-32 w-32 -rotate-90">
            <circle cx="60" cy="60" r="{{ $radio }}" fill="none" style="stroke: var(--ring-track)" stroke-width="10" />
            <circle cx="60" cy="60" r="{{ $radio }}" fill="none" stroke="#0F8A72" stroke-width="10"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circunferencia }}"
                    stroke-dashoffset="{{ $offset }}"
                    style="transition: stroke-dashoffset 0.6s ease" />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="font-datos text-2xl font-bold text-[var(--text-primary)]">{{ $porcentaje }}%</span>
            <span class="text-[10px] font-medium text-[var(--text-muted)] uppercase tracking-wide">Impreso</span>
        </div>
    </div>

    <div class="flex-1 min-w-[200px]">
        <p class="text-sm font-semibold text-[var(--text-primary)] mb-3">Avance de impresión</p>
        <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-[var(--text-muted)]"><span class="h-2 w-2 rounded-full bg-[#0F8A72]"></span>Impresos</span>
                <span class="font-datos font-semibold text-[var(--text-primary)]">{{ number_format($impresos) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-[var(--text-muted)]"><span class="h-2 w-2 rounded-full border border-[var(--border-card)]" style="background: var(--ring-track)"></span>Pendientes</span>
                <span class="font-datos font-semibold text-[var(--text-primary)]">{{ number_format($pendientes) }}</span>
            </div>
            <div class="flex items-center justify-between border-t border-[var(--border-card)] pt-2 mt-2">
                <span class="text-[var(--text-muted)]">Total</span>
                <span class="font-datos font-semibold text-[var(--text-primary)]">{{ number_format($total) }}</span>
            </div>
        </div>
    </div>
</div>
