@if($acuses->count() > 0)
    <div class="overflow-x-auto rounded-xl border border-[var(--border-card)]">
        <table class="min-w-full text-sm">
            <thead class="bg-[var(--hover-tint)]">
                <tr class="text-left text-[var(--text-faint)]">
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Folio</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Nombre completo</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">CURP</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Cuenta</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Fecha Convocatoria</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Fecha Captura</th>
                    <th class="py-2 px-3 font-datos text-xs uppercase tracking-wide">Estatus</th>
                    <th class="py-2 px-3 text-right font-datos text-xs uppercase tracking-wide">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-card)]">
                @foreach($acuses as $acuse)
                    @php
                        $datosIncidencia = $acuse->incidencia ? [
                            'folio' => $acuse->cuarta_linea,
                            'nombre' => $acuse->nombre_completo,
                            'incidencia' => [
                                'id' => $acuse->incidencia->id,
                                'situacion' => $acuse->incidencia->situacion,
                                'accion' => $acuse->incidencia->accion,
                                'comentarios' => $acuse->incidencia->comentarios,
                                'estatus' => $acuse->incidencia->estatus,
                            ],
                        ] : [
                            'folio' => $acuse->cuarta_linea,
                            'nombre' => $acuse->nombre_completo,
                            'curp' => $acuse->curp,
                            'cuenta' => $acuse->cuenta,
                        ];
                        $eventoIncidencia = $acuse->incidencia ? 'ver-incidencia' : 'reportar-incidencia';

                        $datosTarjeta = $acuse->nuevaTarjeta ? [
                            'folio' => $acuse->cuarta_linea,
                            'nombre' => $acuse->nombre_completo,
                            'cuentaAnterior' => $acuse->cuenta,
                            'reasignada' => true,
                            'nuevaCuenta' => $acuse->nuevaTarjeta->nueva_cuenta,
                            'nuevaTarjeta' => $acuse->nuevaTarjeta->nueva_tarjeta,
                            'motivo' => $acuse->nuevaTarjeta->motivo_reasignacion,
                        ] : [
                            'folio' => $acuse->cuarta_linea,
                            'nombre' => $acuse->nombre_completo,
                            'cuentaAnterior' => $acuse->cuenta,
                            'reasignada' => false,
                        ];
                        $eventoTarjeta = $acuse->nuevaTarjeta ? 'ver-tarjeta' : 'asignar-tarjeta';
                    @endphp
                    <tr class="even:bg-[var(--hover-tint)] {{ $acuse->incidencia ? 'bg-[#E4572E]/[0.06]' : '' }}">
                        <td class="py-2 px-3 font-datos font-bold text-[#E4572E]">
                            {{ $acuse->cuarta_linea }}
                            @if($acuse->incidencia)
                                <span title="Tiene una incidencia registrada">⚠️</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 uppercase text-[var(--text-primary)]">{{ $acuse->nombre_completo }}</td>
                        <td class="py-2 px-3 font-datos text-[var(--text-muted)]">{{ $acuse->curp }}</td>
                        <td class="py-2 px-3 font-datos text-[var(--text-muted)]">{{ $acuse->cuenta ?? 'Sin cuenta' }}</td>
                        <td class="py-2 px-3 text-[var(--text-muted)] whitespace-nowrap">
                            {{ $acuse->nss_issemym ? $acuse->nss_issemym->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="py-2 px-3 text-[var(--text-muted)] whitespace-nowrap">
                            @if($acuse->captura?->fecha_captura)
                                {{ $acuse->captura->fecha_captura->format('d/m/Y') }}
                            @else
                                <span class="text-[var(--text-faint)]">Sin Captura</span>
                            @endif
                        </td>
                        <td class="py-2 px-3"><x-status-badge :estatus="$acuse->estatus_cruce" :stock="$acuse->tarjetaStock" /></td>
                        <td class="py-2 px-3 text-right space-x-1">
                            <a href="{{ route('acuses.imprimir_individual', $acuse->cuarta_linea) }}"
                               target="_blank"
                               class="inline-flex items-center rounded-lg bg-[var(--hover-tint)] px-2.5 py-1.5 text-xs font-semibold text-[var(--text-muted)] hover:bg-[var(--border-card)]">
                                Imprimir
                            </a>
                            <button type="button"
                                    @click="$dispatch('{{ $eventoIncidencia }}', @js($datosIncidencia))"
                                    class="inline-flex items-center rounded-lg px-2.5 py-1.5 text-xs font-semibold
                                           {{ $acuse->incidencia ? 'bg-[var(--hover-tint)] text-[var(--text-muted)] hover:bg-[var(--border-card)]' : 'bg-[#E4572E]/10 text-[#E4572E] hover:bg-[#E4572E]/20' }}">
                                {{ $acuse->incidencia ? 'Ver incidencia' : 'Reportar incidencia' }}
                            </button>
                            <button type="button"
                                    @click="$dispatch('{{ $eventoTarjeta }}', @js($datosTarjeta))"
                                    class="inline-flex items-center rounded-lg px-2.5 py-1.5 text-xs font-semibold
                                           {{ $acuse->nuevaTarjeta ? 'bg-[var(--hover-tint)] text-[var(--text-muted)] hover:bg-[var(--border-card)]' : 'bg-[#0F8A72]/10 text-[#0F8A72] hover:bg-[#0F8A72]/20' }}">
                                {{ $acuse->nuevaTarjeta ? 'Ver tarjeta' : 'Asignar tarjeta' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($acuses instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">{{ $acuses->links() }}</div>
    @endif
@else
    <div class="text-center text-[var(--text-faint)] py-8 text-sm">
        No se encontraron coincidencias.
    </div>
@endif
