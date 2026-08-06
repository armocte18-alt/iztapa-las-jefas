{{-- Uso: <x-stat-card titulo="Impresos" :valor="$impresos" color="esmeralda" icono="check" /> --}}
@props(['titulo', 'valor', 'color' => 'esmeralda', 'icono' => 'documentos'])

@php
    $colores = [
        'esmeralda' => ['chip' => 'bg-[#0F8A72]/10 text-[#0F8A72]', 'borde' => '#0F8A72'],
        'navy' => ['chip' => 'bg-[var(--hover-tint)] text-[var(--text-primary)]', 'borde' => '#334155'],
        'indigo' => ['chip' => 'bg-[#4F46E5]/10 text-[#4F46E5]', 'borde' => '#4F46E5'],
        'vermilion' => ['chip' => 'bg-[#E4572E]/10 text-[#E4572E]', 'borde' => '#E4572E'],
    ];
    $c = $colores[$color] ?? $colores['esmeralda'];

    $rutasIcono = [
        'documentos' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'reloj' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'caja' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    ];
    $ruta = $rutasIcono[$icono] ?? $rutasIcono['documentos'];
@endphp

<div class="h-full flex flex-col justify-center rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5 hover:shadow-[0_4px_12px_rgba(16,24,40,0.08)] transition-shadow" style="border-top-color: {{ $c['borde'] }}">
    <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $c['chip'] }} mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ruta }}" />
        </svg>
    </div>
    <p class="text-[13px] font-medium text-[var(--text-muted)]">{{ $titulo }}</p>
    <p class="font-datos text-[28px] font-bold text-[var(--text-primary)] leading-tight mt-0.5">{{ $valor }}</p>
</div>
