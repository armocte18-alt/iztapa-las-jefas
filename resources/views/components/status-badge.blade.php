{{-- Uso: <x-status-badge :estatus="$acuse->estatus_cruce" :stock="$acuse->tarjetaStock" /> --}}
@props(['estatus', 'stock' => null])

@php
    $estilos = [
        'Tarjeta Reasignada' => ['fondo' => 'bg-[#4F46E5]/10', 'texto' => 'text-[#4F46E5]', 'punto' => 'bg-[#4F46E5]'],
        'Tarjeta Entregada' => ['fondo' => 'bg-[#0F8A72]/10', 'texto' => 'text-[#0F8A72]', 'punto' => 'bg-[#0F8A72]'],
        'En Stock' => ['fondo' => 'bg-[var(--hover-tint)]', 'texto' => 'text-[var(--text-primary)]', 'punto' => 'bg-[var(--text-muted)]'],
        'Pendiente' => ['fondo' => 'bg-[#E4572E]/10', 'texto' => 'text-[#E4572E]', 'punto' => 'bg-[#E4572E]'],
    ];
    // Por si llega vacío/null desde algún lugar, nunca se queda sin texto ni color.
    $estatusMostrado = $estatus ?: 'Pendiente';
    $e = $estilos[$estatusMostrado] ?? $estilos['Pendiente'];
@endphp

@if($estatusMostrado === 'En Stock' && $stock)
    <div x-data="{ abierto: false }" class="relative inline-block">
        <button type="button" @click="abierto = !abierto" @click.outside="abierto = false"
                class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $e['fondo'] }} {{ $e['texto'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $e['punto'] }}"></span>
            {{ $estatusMostrado }}
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </button>

        <div x-show="abierto" x-cloak x-transition
             class="absolute z-20 mt-1 w-56 rounded-xl border border-[var(--border-card)] bg-[var(--bg-card)] p-3 text-xs shadow-lg text-[var(--text-primary)]">
            <p><span class="text-[var(--text-muted)]">Caja:</span> <span class="font-medium">{{ $stock->caja ?? 'N/A' }}</span></p>
            <p><span class="text-[var(--text-muted)]">Paquete:</span> <span class="font-medium">{{ $stock->paquete ?? 'N/A' }}</span></p>
            <hr class="my-1.5 border-[var(--border-card)]">
            <p><span class="text-[var(--text-muted)]">Comentarios:</span> {{ $stock->comentarios ?: 'Sin comentarios.' }}</p>
            <p><span class="text-[var(--text-muted)]">Observaciones:</span> {{ $stock->observaciones ?: 'Sin observaciones.' }}</p>
        </div>
    </div>
@else
    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $e['fondo'] }} {{ $e['texto'] }}">
        <span class="h-1.5 w-1.5 rounded-full {{ $e['punto'] }}"></span>
        {{ $estatusMostrado }}
    </span>
@endif
