<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema Acuses') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    <script>
        if (localStorage.getItem('tema') === 'oscuro' ||
            (!localStorage.getItem('tema') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif; }
        .font-datos { font-family: 'IBM Plex Mono', ui-monospace, SFMono-Regular, monospace; }

        :root {
            --bg-page: #F6F7F9; --bg-card: #FFFFFF; --border-card: rgba(16,24,40,0.06);
            --text-primary: #101828; --text-muted: #667085; --text-faint: #98A2B3;
            --hover-tint: rgba(16,24,40,0.025);
        }
        .dark {
            --bg-page: #0A0F1A; --bg-card: #141B2B; --border-card: rgba(255,255,255,0.08);
            --text-primary: #F1F3F7; --text-muted: #98A2B3; --text-faint: #667085;
            --hover-tint: rgba(255,255,255,0.04);
        }
        input, select, textarea { background-color: var(--bg-card); color: var(--text-primary); border-color: var(--border-card); }
        .dark input, .dark select, .dark textarea { color-scheme: dark; }

        /* Marca de agua institucional de fondo — muy tenue, para que el
           formulario siga siendo perfectamente legible encima. */
        .marca-agua {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('logo.png') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.06;
            filter: grayscale(30%);
            pointer-events: none;
        }
        .dark .marca-agua { opacity: 0.10; }
    </style>
</head>
<body class="h-full bg-[var(--bg-page)] text-[var(--text-primary)] antialiased">
    <div class="marca-agua"></div>

    <div class="relative min-h-full flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="font-bold text-xl text-[var(--text-primary)]">Sistema Acuses</h1>
                <p class="text-sm text-[var(--text-muted)] mt-1">Iztapa'Las Jefas 2026 · Iztapalapa</p>
            </div>

            <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] shadow-[0_4px_24px_rgba(16,24,40,0.08)] p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
