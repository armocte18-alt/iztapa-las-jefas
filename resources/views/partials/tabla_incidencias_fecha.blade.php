<div class="flex items-center justify-between mb-2">
    <p class="text-xs text-[var(--text-faint)]">{{ $incidencias->total() }} incidencia(s) encontradas</p>
</div>

@if($incidencias->count() > 0)
    <div class="overflow-x-auto rounded-xl border border-[var(--border-card)]">
        <table class="min-w-full text-sm">
            <thead class="bg-[#101828]/[0.02]">
                <tr class="text-left text-[var(--text-faint)]">
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Folio / Cuenta</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Fecha</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Situación</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Acción</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Comentarios</th>
                    <th class="py-2 px-3 text-center font-datos text-xs uppercase tracking-wide">Estatus</th>
                    <th class="py-2 px-3 text-center font-datos text-xs uppercase tracking-wide">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-card)]">
                @foreach($incidencias as $inc)
                    <tr class="even:bg-[var(--hover-tint)]">
                        <td class="py-2 px-3">
                            <div class="font-datos font-bold text-[#E4572E]">{{ $inc->folio }}</div>
                            <div class="text-xs font-datos text-[var(--text-faint)]">{{ $inc->acuse->cuenta ?? 'No Asignada' }}</div>
                        </td>
                        <td class="py-2 px-3 whitespace-nowrap text-[var(--text-muted)]">{{ $inc->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3">{{ $inc->situacion }}</td>
                        <td class="py-2 px-3">{{ $inc->accion }}</td>
                        <td class="py-2 px-3 text-[var(--text-muted)] max-w-xs truncate" title="{{ $inc->comentarios }}">{{ $inc->comentarios ?? 'Sin observaciones.' }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($inc->estatus === 'Atendido')
                                <span class="inline-flex items-center rounded-full bg-[#0F8A72]/10 px-2.5 py-1 font-datos text-[11px] font-semibold text-[#0F8A72]">Atendido</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-[#E4572E]/10 px-2.5 py-1 font-datos text-[11px] font-semibold text-[#E4572E]">Pendiente</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($inc->estatus !== 'Atendido')
                                <button type="button"
                                        @click="atender({{ $inc->id }})"
                                        class="rounded-lg bg-[#0F8A72] px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-[#0C6F5B]">
                                    Marcar atendida
                                </button>
                            @else
                                <span class="text-[var(--text-faint)] text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $incidencias->links() }}</div>
@else
    <div class="text-center text-[var(--text-faint)] py-8 text-sm">
        Sin incidencias que coincidan con los criterios de búsqueda.
    </div>
@endif
