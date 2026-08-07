<x-guest-layout>
    <h2 class="text-xl font-bold text-[var(--text-primary)] mb-1">Iniciar sesión</h2>
    <p class="text-sm text-[var(--text-muted)] mb-6">Sistema Acuses — Iztapa'Las Jefas 2026</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <x-checkbox id="remember_me" name="remember" />
                <span class="text-sm text-[var(--text-muted)]">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#0F8A72] hover:text-[#0C6F5B] font-medium" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center">
            Entrar
        </x-primary-button>

        @if (Route::has('register'))
            <p class="text-center text-sm text-[var(--text-muted)] pt-2">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-[#0F8A72] hover:text-[#0C6F5B] font-medium">Regístrate</a>
            </p>
        @endif
    </form>
</x-guest-layout>

<!-- PRUEBA-UNICA-12345 -->
