<x-layouts.app titulo="Inicio">
    <div x-data="panelPrincipal()" x-init="init()" class="space-y-10">

        {{-- Avance + indicadores --}}
        <div class="grid grid-cols-6 gap-5 items-stretch">
            <div class="col-span-2 animar-entrada" style="animation-delay: 0ms">
                <x-avance-ring :total="$total" :impresos="$impresos" />
            </div>
            <div class="animar-entrada h-full" style="animation-delay: 60ms"><x-stat-card titulo="Total de Acuses" :valor="number_format($total)" color="navy" icono="documentos" /></div>
            <div class="animar-entrada h-full" style="animation-delay: 120ms"><x-stat-card titulo="Pendientes" :valor="number_format($pendientes)" color="vermilion" icono="reloj" /></div>
            <div class="animar-entrada h-full" style="animation-delay: 180ms"><x-stat-card titulo="Cajas Restantes" :valor="number_format($cajasRestantes)" color="indigo" icono="caja" /></div>
            <div class="animar-entrada h-full" style="animation-delay: 240ms"><x-stat-card titulo="Impresos" :valor="number_format($impresos)" color="esmeralda" icono="check" /></div>
        </div>

        {{-- Búsqueda --}}
        <div id="buscar" class="scroll-mt-28 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5" style="border-top-color: #0F8A72">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#0F8A72]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <h2 class="text-sm font-bold text-[var(--text-primary)]">Buscar beneficiaria</h2>
            </div>
            <form @submit.prevent="buscarBeneficiaria()" class="flex gap-3">
                <input type="text" x-model="busqueda.termino" @input.debounce.400ms="buscarBeneficiaria()"
                       placeholder="Folio, cuenta, nombre o CURP..."
                       class="flex-1 rounded-lg border-[#101828]/15 shadow-sm text-sm focus:border-[#0F8A72] focus:ring-[#0F8A72]">
                <button type="submit"
                        class="rounded-lg bg-[#0F8A72] px-5 py-2 text-sm font-semibold text-white hover:bg-[#0C6F5B] shadow-sm transition">
                    Buscar
                </button>
            </form>

            <div class="mt-5" x-show="busqueda.termino.trim() !== ''" x-cloak x-html="busqueda.html"></div>
        </div>

        {{-- Acciones rápidas --}}
        <div id="acciones" class="scroll-mt-28">
            <p class="text-xs font-bold uppercase tracking-wider text-[var(--text-faint)] mb-3">Acciones rápidas</p>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <x-upload-dropzone
                    :action="route('import')"
                    campo="file"
                    aceptar=".xlsx,.xls,.csv"
                    titulo="Importar Excel maestro de acuses"
                    ayuda="Datos completos de cada beneficiaria (folio, nombre, CURP, cuenta...)."
                    texto-boton="Cargar"
                    color="esmeralda"
                />

                <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5 hover:shadow-md hover:-translate-y-0.5 transition-all" style="border-top-color: #4F46E5" x-data="{ modo: 'caja' }">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#4F46E5]/10 text-[#4F46E5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2M5 17H3a2 2 0 01-2-2v-4a2 2 0 012-2h2m10-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0H7m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V7" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)] pt-1.5">Imprimir acuses</h2>
                    </div>

                    <div class="flex gap-1 mb-3 rounded-lg bg-[var(--hover-tint)] p-1 text-xs font-semibold w-fit">
                        <button type="button" @click="modo = 'caja'"
                                :class="modo === 'caja' ? 'bg-[var(--bg-card)] text-[var(--text-primary)] shadow-sm' : 'text-[var(--text-muted)]'"
                                class="rounded-md px-3 py-1.5 transition">Siguiente caja</button>
                        <button type="button" @click="modo = 'rango'"
                                :class="modo === 'rango' ? 'bg-[var(--bg-card)] text-[var(--text-primary)] shadow-sm' : 'text-[var(--text-muted)]'"
                                class="rounded-md px-3 py-1.5 transition">Rango de folios</button>
                    </div>

                    <form x-show="modo === 'caja'" method="GET" action="{{ route('download.pdf') }}" target="_blank" class="flex flex-wrap gap-2">
                        <select name="tipo" class="rounded-lg border-[var(--border-card)] text-sm">
                            <option value="carta_1">Carta · 1 copia</option>
                            <option value="carta_2">Carta · 2 copias</option>
                            <option value="oficio_1">Oficio · 1 copia</option>
                            <option value="oficio_2" selected>Oficio · 2 copias</option>
                        </select>
                        <button class="rounded-lg bg-[#4F46E5] px-4 py-2 text-sm font-semibold text-white hover:bg-[#4338CA] shadow-sm transition">
                            Descargar PDF
                        </button>
                    </form>

                    <form x-show="modo === 'rango'" x-cloak method="GET" action="{{ route('download.pdf') }}" target="_blank" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tipo" value="rango">
                        <input type="number" name="folio_inicio" required min="1" placeholder="Folio inicio"
                               class="w-28 rounded-lg border-[var(--border-card)] text-sm">
                        <span class="text-[var(--text-faint)]">–</span>
                        <input type="number" name="folio_fin" required min="1" placeholder="Folio fin"
                               class="w-28 rounded-lg border-[var(--border-card)] text-sm">
                        <button class="rounded-lg bg-[#4F46E5] px-4 py-2 text-sm font-semibold text-white hover:bg-[#4338CA] shadow-sm transition">
                            Descargar PDF
                        </button>
                    </form>
                    <p x-show="modo === 'rango'" x-cloak class="text-xs text-[var(--text-muted)] mt-2">
                        Imprime en oficio a 2 copias, sin afectar el conteo general de cajas.
                    </p>
                </div>

                <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5 hover:shadow-md hover:-translate-y-0.5 transition-all" style="border-top-color: #334155">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[var(--hover-tint)] text-[var(--text-primary)]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 2v8m0 0v2m0-2c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)] pt-1.5">Tarjetas reasignadas</h2>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mb-3">Reporte de todas las tarjetas y cuentas reasignadas hasta hoy.</p>
                    <a href="{{ route('tarjetas.excel') }}"
                       class="inline-flex items-center rounded-lg bg-[#101828] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B121D] shadow-sm transition">
                        Exportar a Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- Inventario y capturas --}}
        <div id="inventario" class="scroll-mt-28">
            <p class="text-xs font-bold uppercase tracking-wider text-[var(--text-faint)] mb-3">Inventario y capturas</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <x-upload-dropzone
                    :action="route('tarjetas.cargar_stock')"
                    campo="archivo_excel"
                    aceptar=".xlsx,.xls"
                    titulo="Cargar inventario de tarjetas físicas"
                    ayuda="Columnas: folio, caja, paquete, comentarios, observaciones. REEMPLAZA todo el stock actual."
                    texto-boton="Cargar stock"
                    color="navy"
                    confirmar="Esto reemplaza TODO el inventario de stock actual. ¿Continuar?"
                />

                <x-upload-dropzone
                    :action="route('import.capturas')"
                    campo="archivo_excel"
                    aceptar=".xlsx,.xls,.csv"
                    titulo="Importar capturas (entregas confirmadas)"
                    ayuda="Columnas: folio, cuenta, curp, fecha_captura. Actualiza o agrega — no borra existentes."
                    texto-boton="Importar"
                    color="esmeralda"
                />
            </div>
        </div>

        {{-- Incidencias --}}
        <div id="incidencias" class="scroll-mt-28 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5" style="border-top-color: #E4572E">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#E4572E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Incidencias</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="date" x-model="filtros.fecha_inicio" @change="buscarIncidencias()"
                           class="rounded-lg border-[#101828]/15 font-datos text-xs py-1.5">
                    <input type="date" x-model="filtros.fecha_fin" @change="buscarIncidencias()"
                           class="rounded-lg border-[#101828]/15 font-datos text-xs py-1.5">
                    <input type="text" x-model="filtros.buscar" @input.debounce.400ms="buscarIncidencias()"
                           placeholder="Filtrar por folio..."
                           class="rounded-lg border-[#101828]/15 text-xs py-1.5">
                    <a :href="`{{ route('incidencias.excel') }}?fecha_inicio=${filtros.fecha_inicio}&fecha_fin=${filtros.fecha_fin}`" class="text-xs font-semibold text-[#0F8A72] hover:text-[#0C6F5B]">Excel</a>
                    <a :href="`{{ route('incidencias.pdf') }}?fecha_inicio=${filtros.fecha_inicio}&fecha_fin=${filtros.fecha_fin}`" class="text-xs font-semibold text-[#0F8A72] hover:text-[#0C6F5B]">PDF</a>
                </div>
            </div>

            <div x-html="incidenciasHtml"></div>
        </div>

        {{-- Historial --}}
        <div id="historial" class="scroll-mt-28 rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] border-t-[3px] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-5" style="border-top-color: #D97706">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--text-faint)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l4-4 4 4 5-5" />
                    </svg>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Historial de ciclos de carga</h2>
                </div>
                <a href="{{ route('export.historial') }}" class="text-sm font-semibold text-[#0F8A72] hover:text-[#0C6F5B]">
                    Exportar a Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-[var(--text-faint)] border-b border-[var(--border-card)]">
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide">Ciclo</th>
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide">Programadas</th>
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide">Capturadas</th>
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide w-40">Avance</th>
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide">Folio inicial</th>
                            <th class="py-2 pr-4 font-datos text-xs uppercase tracking-wide">Folio final</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-card)]">
                        @forelse($historial as $ciclo)
                            @php
                                $avanceCiclo = $ciclo->total_registros > 0
                                    ? min(100, round(($ciclo->total_capturados / $ciclo->total_registros) * 100))
                                    : 0;
                            @endphp
                            <tr class="even:bg-[var(--hover-tint)] hover:bg-[var(--border-card)] transition-colors">
                                <td class="py-2.5 pr-4">{{ \Carbon\Carbon::parse($ciclo->fecha_carga)->format('d/m/Y') }}</td>
                                <td class="py-2.5 pr-4 font-datos">{{ number_format($ciclo->total_registros) }}</td>
                                <td class="py-2.5 pr-4 font-datos">{{ number_format($ciclo->total_capturados) }}</td>
                                <td class="py-2.5 pr-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 rounded-full" style="background: var(--ring-track)">
                                            <div class="h-1.5 rounded-full bg-[#0F8A72]" style="width: {{ $avanceCiclo }}%"></div>
                                        </div>
                                        <span class="font-datos text-xs text-[var(--text-muted)] w-9 text-right">{{ $avanceCiclo }}%</span>
                                    </div>
                                </td>
                                <td class="py-2.5 pr-4 font-datos text-[var(--text-muted)]">{{ $ciclo->folio_inicial }}</td>
                                <td class="py-2.5 pr-4 font-datos text-[var(--text-muted)]">{{ $ciclo->folio_final }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-[var(--text-faint)]">Sin ciclos registrados todavía.</td></tr>
                        @endforelse
                    </tbody>
                    @if(count($historial) > 0)
                        <tfoot>
                            <tr class="border-t-2 border-[var(--border-card)] font-semibold">
                                <td class="py-2.5 pr-4 text-[var(--text-primary)]">Total</td>
                                <td class="py-2.5 pr-4 font-datos text-[var(--text-primary)]">{{ number_format($historial->sum('total_registros')) }}</td>
                                <td class="py-2.5 pr-4 font-datos text-[var(--text-primary)]">{{ number_format($historial->sum('total_capturados')) }}</td>
                                <td class="py-2.5 pr-4"></td>
                                <td class="py-2.5 pr-4"></td>
                                <td class="py-2.5 pr-4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <x-modal-incidencia />
        <x-modal-tarjeta />
    </div>

    @push('scripts')
    <script>
        function panelPrincipal() {
            return {
                incidenciasHtml: '<p class="text-sm text-[var(--text-faint)] text-center py-6">Cargando...</p>',
                filtros: {
                    fecha_inicio: new Date().toISOString().slice(0, 10),
                    fecha_fin: new Date().toISOString().slice(0, 10),
                    buscar: '',
                },
                busqueda: {
                    termino: @js(request('buscar', '')),
                    html: '',
                },
                modalIncidencia: {
                    abierto: false, folio: '', nombre: '', curp: '', cuenta: '', incidencia: null,
                },
                modalTarjeta: {
                    abierto: false, folio: '', nombre: '', cuentaAnterior: '', reasignada: false,
                    nuevaCuenta: '', nuevaTarjeta: '', motivo: '',
                },

                init() {
                    this.buscarIncidencias();

                    if (this.busqueda.termino.trim() !== '') {
                        this.buscarBeneficiaria();
                    }

                    window.addEventListener('reportar-incidencia', (e) => {
                        this.modalIncidencia = { ...e.detail, incidencia: null, abierto: true };
                    });
                    window.addEventListener('ver-incidencia', (e) => {
                        this.modalIncidencia = { ...e.detail, abierto: true };
                    });

                    window.addEventListener('asignar-tarjeta', (e) => {
                        this.modalTarjeta = { ...e.detail, abierto: true };
                    });
                    window.addEventListener('ver-tarjeta', (e) => {
                        this.modalTarjeta = { ...e.detail, abierto: true };
                    });
                },

                async buscarIncidencias() {
                    const params = new URLSearchParams({
                        fecha_inicio: this.filtros.fecha_inicio,
                        fecha_fin: this.filtros.fecha_fin,
                        buscar_sub_incidencia: this.filtros.buscar,
                    });
                    const respuesta = await fetch(`{{ route('incidencias.por_fecha') }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const datos = await respuesta.json();
                    this.incidenciasHtml = datos.html_tabla;
                },

                async buscarBeneficiaria() {
                    if (this.busqueda.termino.trim() === '') {
                        this.busqueda.html = '';
                        return;
                    }
                    this.busqueda.html = '<p class="text-sm text-[var(--text-faint)] text-center py-6">Buscando...</p>';

                    const params = new URLSearchParams({ buscar: this.busqueda.termino });
                    const respuesta = await fetch(`{{ route('index') }}?${params}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const datos = await respuesta.json();
                    this.busqueda.html = datos.html;
                },

                async atender(id) {
                    const confirmacion = await window.Swal.fire({
                        title: '¿Marcar como atendida?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, atender',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#0F8A72',
                    });
                    if (!confirmacion.isConfirmed) return;

                    await fetch(`/incidencias/atender/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    this.buscarIncidencias();
                    window.Swal.fire({ icon: 'success', title: 'Incidencia atendida', timer: 1500, showConfirmButton: false });
                },
            };
        }
    </script>
    @endpush
</x-layouts.app>
