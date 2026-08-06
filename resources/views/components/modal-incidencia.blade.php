<div x-show="modalIncidencia.abierto"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-[#101828]/60 px-4"
     @keydown.escape.window="modalIncidencia.abierto = false">

    <div @click.outside="modalIncidencia.abierto = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="w-full max-w-lg rounded-2xl bg-[var(--bg-card)] shadow-2xl">

        <div class="flex items-center justify-between rounded-t-2xl px-5 py-4"
             :class="modalIncidencia.incidencia ? 'bg-[#101828]' : 'bg-[#E4572E]'">
            <h3 class="font-bold text-white" x-text="modalIncidencia.incidencia ? 'Detalle de la incidencia' : 'Reportar incidencia'"></h3>
            <button @click="modalIncidencia.abierto = false" class="text-white/80 hover:text-white">✕</button>
        </div>

        <div class="p-5 space-y-4 text-sm">
            <div class="rounded-xl bg-[var(--bg-page)] p-3 grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs text-[var(--text-muted)]">Folio</p>
                    <p class="font-datos font-bold text-[#E4572E]" x-text="modalIncidencia.folio"></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-muted)]">Beneficiaria</p>
                    <p class="font-medium uppercase text-[var(--text-primary)] text-xs" x-text="modalIncidencia.nombre"></p>
                </div>
            </div>

            <template x-if="modalIncidencia.incidencia">
                <div class="space-y-2">
                    <p><span class="text-[var(--text-muted)]">Situación:</span> <span x-text="modalIncidencia.incidencia.situacion"></span></p>
                    <p><span class="text-[var(--text-muted)]">Acción solicitada:</span> <span x-text="modalIncidencia.incidencia.accion"></span></p>
                    <p><span class="text-[var(--text-muted)]">Comentarios:</span> <span x-text="modalIncidencia.incidencia.comentarios || 'Sin comentarios.'"></span></p>

                    <div class="pt-3 flex justify-end gap-2">
                        <button type="button" @click="modalIncidencia.abierto = false"
                                class="rounded-lg border border-[var(--border-card)] px-4 py-2 text-sm font-semibold text-[var(--text-muted)] hover:bg-[var(--hover-tint)]">
                            Cerrar
                        </button>
                        <button type="button"
                                x-show="modalIncidencia.incidencia.estatus !== 'Atendido'"
                                @click="atender(modalIncidencia.incidencia.id); modalIncidencia.abierto = false"
                                class="rounded-lg bg-[#0F8A72] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C6F5B]">
                            Marcar atendida
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="!modalIncidencia.incidencia">
                <form method="POST" action="{{ route('incidencias.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="folio" :value="modalIncidencia.folio">

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Situación detectada</label>
                        <select name="situacion" required
                                class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#E4572E] focus:ring-[#E4572E]">
                            <option value="" selected disabled>-- Seleccione una situación --</option>
                            <option value="Beneficiaria no puede agregar su tarjeta">Beneficiaria no puede agregar su tarjeta</option>
                            <option value="Error de datos en impresión de acuse">Error de datos en impresión de acuse</option>
                            <option value="Folio duplicado">Folio duplicado</option>
                            <option value="Folio no Capturado">Folio no Capturado</option>
                            <option value="No puede descargar la App">No puede descargar la App</option>
                            <option value="Tarjeta dañada / defectuosa">Tarjeta dañada / defectuosa</option>
                            <option value="Tarjeta entregada a la beneficiaria pero olvidada en mesa">Tarjeta entregada a la beneficiaria pero olvidada en mesa</option>
                            <option value="Tarjeta entregada a otra beneficiaria">Tarjeta entregada a otra beneficiaria</option>
                            <option value="Tarjeta no localizada">Tarjeta no localizada</option>
                            <option value="Tarjeta sin saldo">Tarjeta sin saldo</option>
                            <option value="Otro motivo">Otro motivo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Acción solicitada</label>
                        <select name="accion" required
                                class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#E4572E] focus:ring-[#E4572E]">
                            <option value="" selected disabled>-- Seleccione acción a tomar --</option>
                            <option value="El caso debe ser redirigido con BROXEL">El caso debe ser redirigido con BROXEL</option>
                            <option value="Entrega de tarjeta nueva">Entrega de tarjeta nueva</option>
                            <option value="Especificar Motivo o Acción (Folio no capturado)">Especificar Motivo o Acción (Folio no capturado)</option>
                            <option value="Reimpresión de acuse">Reimpresión de acuse</option>
                            <option value="Solicitud de cancelación o reversa en SIGITEL">Solicitud de cancelación o reversa en SIGITEL</option>
                            <option value="Otra acción">Otra acción</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Comentarios adicionales</label>
                        <textarea name="comentarios" rows="2"
                                  class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#E4572E] focus:ring-[#E4572E]"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="modalIncidencia.abierto = false"
                                class="rounded-lg border border-[var(--border-card)] px-4 py-2 text-sm font-semibold text-[var(--text-muted)] hover:bg-[var(--hover-tint)]">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-[#E4572E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#C7461F]">
                            Guardar incidencia
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
