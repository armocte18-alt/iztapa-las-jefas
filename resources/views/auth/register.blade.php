<x-guest-layout>
    <h2 class="text-xl font-bold text-[var(--text-primary)] mb-1">Crear cuenta</h2>
    <p class="text-sm text-[var(--text-muted)] mb-6">Sistema Acuses — Iztapa'Las Jefas 2026</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nombre completo')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Crear cuenta
        </x-primary-button>

        <p class="text-center text-sm text-[var(--text-muted)] pt-2">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-[#0F8A72] hover:text-[#0C6F5B] font-medium">Inicia sesión</a>
        </p>
    </form>
</x-guest-layout>
