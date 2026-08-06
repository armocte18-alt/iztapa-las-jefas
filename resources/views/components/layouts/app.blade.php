<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $titulo ?? 'Sistema Acuses' }} | Iztapa'Las Jefas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    {{-- Aplica el tema guardado ANTES de pintar la página, para que no
         haya parpadeo blanco→oscuro al cargar. --}}
    <script>
        if (localStorage.getItem('tema') === 'oscuro' ||
            (!localStorage.getItem('tema') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif; }
        .font-datos { font-family: 'IBM Plex Mono', ui-monospace, SFMono-Regular, monospace; font-variant-numeric: tabular-nums; }

        :root {
            --bg-page: #F6F7F9;
            --bg-card: #FFFFFF;
            --border-card: rgba(16, 24, 40, 0.06);
            --text-primary: #101828;
            --text-muted: #667085;
            --text-faint: #98A2B3;
            --hover-tint: rgba(16, 24, 40, 0.025);
            --ring-track: #EEF0F2;
        }
        .dark {
            --bg-page: #0A0F1A;
            --bg-card: #141B2B;
            --border-card: rgba(255, 255, 255, 0.08);
            --text-primary: #F1F3F7;
            --text-muted: #98A2B3;
            --text-faint: #667085;
            --hover-tint: rgba(255, 255, 255, 0.04);
            --ring-track: #232B3D;
        }

        /* Los inputs heredan el tema automáticamente, sin tocar cada formulario */
        input, select, textarea {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border-card);
        }
        .dark input, .dark select, .dark textarea { color-scheme: dark; }
        .dark ::placeholder { color: #56607a; }

        /* Entrada suave y escalonada de las tarjetas al cargar la página */
        @keyframes subir-y-aparecer {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animar-entrada { animation: subir-y-aparecer 0.5s ease-out both; }
    </style>
</head>
<body class="h-full bg-[var(--bg-page)] text-[var(--text-primary)] antialiased transition-colors duration-300">
    <div x-data="{ menuAbierto: false, oscuro: document.documentElement.classList.contains('dark') }" class="min-h-full">

        {{-- Header --}}
        <header class="relative overflow-hidden bg-[#0B2E27] sticky top-0 z-40">
            {{-- Un toque de vida: resplandor sutil de gradiente, no un banner plano --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_120%_at_15%_0%,rgba(15,138,114,0.35),transparent)]"></div>

            <div class="relative mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col leading-tight">
                            <span class="text-white font-bold text-[15px] tracking-tight">Sistema Acuses</span>
                            <span class="text-white/50 text-[11px] font-medium">Iztapa'Las Jefas 2026</span>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center gap-3">
                        {{-- Interruptor claro/oscuro --}}
                        <button type="button"
                                @click="oscuro = !oscuro; document.documentElement.classList.toggle('dark', oscuro); localStorage.setItem('tema', oscuro ? 'oscuro' : 'claro')"
                                class="relative flex h-8 w-8 items-center justify-center rounded-md bg-white/10 hover:bg-white/15 text-white transition"
                                title="Cambiar tema claro / oscuro">
                            <svg x-show="!oscuro" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="oscuro" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <span class="text-white/60 text-sm">{{ auth()->user()->name }}
                            <span class="text-white/40">· {{ auth()->user()->rol }}</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md bg-white/10 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/15 transition">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>

                    <button @click="menuAbierto = !menuAbierto" class="sm:hidden text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div x-show="menuAbierto" x-cloak class="sm:hidden pb-4 flex flex-col gap-3">
                    <button type="button"
                            @click="oscuro = !oscuro; document.documentElement.classList.toggle('dark', oscuro); localStorage.setItem('tema', oscuro ? 'oscuro' : 'claro')"
                            class="self-start rounded-md bg-white/10 px-3 py-1.5 text-xs font-semibold text-white">
                        <span x-text="oscuro ? '☀ Modo claro' : '🌙 Modo oscuro'"></span>
                    </button>
                    <span class="text-white/60 text-sm">{{ auth()->user()->name }} ({{ auth()->user()->rol }})</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-white/60 hover:text-white">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <nav class="relative hidden sm:block border-t border-white/10">
                <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
                    <div class="flex gap-6 text-[13px] font-semibold">
                        <a href="#buscar" class="py-2.5 text-white/50 hover:text-white transition">Buscar</a>
                        <a href="#acciones" class="py-2.5 text-white/50 hover:text-white transition">Acciones rápidas</a>
                        <a href="#inventario" class="py-2.5 text-white/50 hover:text-white transition">Inventario</a>
                        <a href="#incidencias" class="py-2.5 text-white/50 hover:text-white transition">Incidencias</a>
                        <a href="#historial" class="py-2.5 text-white/50 hover:text-white transition">Historial</a>
                    </div>
                </div>
            </nav>
        </header>

        {{-- Membrete institucional: logos a tamaño real, entre el navbar y el contenido --}}
        <div class="border-b border-[var(--border-card)] bg-[var(--bg-card)]">
            <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-center">
                <img src="{{ asset('logo_institucional.png') }}" alt="Membrete institucional"
                     class="h-14 object-contain" onerror="this.remove()">
            </div>
        </div>

        @if (session('success'))
            <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-[#0F8A72]/10 border border-[#0F8A72]/20 p-3 text-sm text-[#0F8A72] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Antes faltaba: back()->with('error', ...) nunca se mostraba en pantalla,
             así que un formulario podía fallar "en silencio" para quien lo usa. --}}
        @if (session('error'))
            <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-[#E4572E]/10 border border-[#E4572E]/20 p-3 text-sm text-[#E4572E] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-[#E4572E]/10 border border-[#E4572E]/20 p-3 text-sm text-[#E4572E]">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <main class="mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')

    @if (session('swal_incidencia_guardada'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.Swal.fire({
                    title: '¡Listo!',
                    text: @json(session('swal_incidencia_guardada')),
                    icon: 'success',
                    confirmButtonColor: '#0F8A72',
                    background: document.documentElement.classList.contains('dark') ? '#141B2B' : '#FFFFFF',
                    color: document.documentElement.classList.contains('dark') ? '#F1F3F7' : '#101828',
                });
            });
        </script>
    @endif
</body>
</html>
