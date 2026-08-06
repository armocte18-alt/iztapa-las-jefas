@props([
    'action', 'campo', 'aceptar', 'titulo', 'ayuda', 'textoBoton' => 'Cargar',
    'color' => 'esmeralda', 'confirmar' => null,
])

@php
    $colores = [
        'esmeralda' => ['icono' => 'bg-[#0F8A72]/10 text-[#0F8A72]', 'boton' => 'bg-[#0F8A72] hover:bg-[#0C6F5B]', 'zona' => 'hover:border-[#0F8A72]/40 hover:bg-[#0F8A72]/[0.03]', 'zonaActiva' => 'border-[#0F8A72]/40 bg-[#0F8A72]/[0.04] text-[#0F8A72]', 'borde' => '#0F8A72'],
        'navy' => ['icono' => 'bg-[var(--hover-tint)] text-[var(--text-primary)]', 'boton' => 'bg-[#101828] hover:bg-[#0B121D]', 'zona' => 'hover:border-[var(--border-card)] hover:bg-[var(--hover-tint)]', 'zonaActiva' => 'border-[var(--border-card)] bg-[var(--hover-tint)] text-[var(--text-primary)]', 'borde' => '#334155'],
        'indigo' => ['icono' => 'bg-[#4F46E5]/10 text-[#4F46E5]', 'boton' => 'bg-[#4F46E5] hover:bg-[#4338CA]', 'zona' => 'hover:border-[#4F46E5]/40 hover:bg-[#4F46E5]/[0.03]', 'zonaActiva' => 'border-[#4F46E5]/40 bg-[#4F46E5]/[0.04] text-[#4F46E5]', 'borde' => '#4F46E5'],
    ];
    $c = $colores[$color] ?? $colores['esmeralda'];
@endphp

<div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5 hover:shadow-md hover:-translate-y-0.5 transition-all" style="border-top-color: {{ $c['borde'] }}">
    <div class="flex items-start gap-3 mb-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $c['icono'] }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3" />
            </svg>
        </div>
        <div>
            <h2 class="text-sm font-bold text-[var(--text-primary)]">{{ $titulo }}</h2>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $ayuda }}</p>
        </div>
    </div>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data"
          x-data="{ nombreArchivo: null }"
          @if($confirmar) onsubmit="return confirm('{{ $confirmar }}');" @endif
          class="flex items-center gap-3">
        @csrf

        <label class="flex-1 cursor-pointer">
            <div class="flex items-center gap-2 rounded-lg border border-dashed border-[#101828]/15 px-3 py-2.5 text-sm transition {{ $c['zona'] }}"
                 :class="nombreArchivo ? '{{ $c['zonaActiva'] }}' : 'text-[var(--text-faint)]'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="truncate font-datos text-xs" x-text="nombreArchivo || 'Seleccionar archivo...'"></span>
            </div>
            <input type="file" name="{{ $campo }}" required accept="{{ $aceptar }}" class="hidden"
                   @change="nombreArchivo = $event.target.files[0]?.name">
        </label>

        <button type="submit" class="shrink-0 rounded-lg {{ $c['boton'] }} px-4 py-2.5 text-sm font-semibold text-white transition">
            {{ $textoBoton }}
        </button>
    </form>
</div>
