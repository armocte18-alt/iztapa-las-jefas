<div x-show="modalTarjeta.abierto"
     x-cloak
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center bg-[#101828]/60 px-4"
     @keydown.escape.window="modalTarjeta.abierto = false">

    <div @click.outside="modalTarjeta.abierto = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="w-full max-w-lg rounded-2xl bg-[var(--bg-card)] shadow-2xl">

        <div class="flex items-center justify-between rounded-t-2xl px-5 py-4"
             :class="modalTarjeta.reasignada ? 'bg-[#101828]' : 'bg-[#0F8A72]'">
            <h3 class="font-bold text-white" x-text="modalTarjeta.reasignada ? 'Control de reasignación' : 'Asignar nueva tarjeta / cuenta'"></h3>
            <button @click="modalTarjeta.abierto = false" class="text-white/80 hover:text-white">✕</button>
        </div>

        <div class="p-5 space-y-4 text-sm">
            <div class="rounded-xl bg-[var(--bg-page)] p-3 grid grid-cols-2 gap-2">
                <div>
                    <p class="text-xs text-[var(--text-muted)]">Folio</p>
                    <p class="font-datos font-bold text-[#E4572E]" x-text="modalTarjeta.folio"></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-muted)]">Beneficiaria</p>
                    <p class="font-medium uppercase text-[var(--text-primary)] text-xs" x-text="modalTarjeta.nombre"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-[var(--text-muted)]">Cuenta / Tarjeta anterior</p>
                    <p class="font-datos text-[var(--text-muted)]" x-text="modalTarjeta.cuentaAnterior || 'Sin cuenta'"></p>
                </div>
            </div>

            <template x-if="modalTarjeta.reasignada">
                <div class="space-y-2">
                    <div class="rounded-xl border border-[#0F8A72]/20 bg-[#0F8A72]/5 p-3">
                        <p><span class="text-[var(--text-muted)]">Nueva cuenta:</span> <span class="font-datos font-bold text-[#0F8A72]" x-text="modalTarjeta.nuevaCuenta"></span></p>
                        <p><span class="text-[var(--text-muted)]">Nueva tarjeta (últimos 4):</span> <span class="font-datos font-bold text-[#0F8A72]">**** **** **** <span x-text="modalTarjeta.nuevaTarjeta"></span></span></p>
                        <p><span class="text-[var(--text-muted)]">Motivo:</span> <span x-text="modalTarjeta.motivo"></span></p>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="button" @click="modalTarjeta.abierto = false"
                                class="rounded-lg border border-[var(--border-card)] px-4 py-2 text-sm font-semibold text-[var(--text-muted)] hover:bg-[var(--hover-tint)]">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="!modalTarjeta.reasignada">
                <form method="POST" action="{{ route('tarjetas.asignar') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="folio" :value="modalTarjeta.folio">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Nueva cuenta (10 dígitos)</label>
                            <input type="text" name="nueva_cuenta" required maxlength="10" pattern="\d{10}"
                                   placeholder="223666980"
                                   class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Nueva tarjeta (últimos 4)</label>
                            <input type="text" name="nueva_tarjeta" required maxlength="4" pattern="\d{4}"
                                   placeholder="1515"
                                   class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Teléfono de contacto</label>
                            <input type="text" name="telefono" required
                                   placeholder="55-3722-0966"
                                   class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Correo electrónico</label>
                            <input type="email" name="correo_electronico" required
                                   placeholder="correo@correo.com"
                                   class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1">Motivo de reasignación</label>
                        <select name="motivo_reasignacion" required
                                class="w-full rounded-lg border-[#101828]/15 text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                            <option value="">Selecciona un motivo...</option>
                            <option value="Tarjeta entregada a otra beneficiaria">Tarjeta entregada a otra beneficiaria</option>
                            <option value="Tarjeta no localizada">Tarjeta no localizada</option>
                            <option value="Tarjeta dañada">Tarjeta dañada</option>
                        </select>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="modalTarjeta.abierto = false"
                                class="rounded-lg border border-[var(--border-card)] px-4 py-2 text-sm font-semibold text-[var(--text-muted)] hover:bg-[var(--hover-tint)]">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-lg bg-[#0F8A72] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0C6F5B]">
                            Guardar asignación
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
