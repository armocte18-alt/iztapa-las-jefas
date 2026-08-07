<x-guest-layout>
    <h2 class="text-xl font-bold text-[var(--text-primary)] mb-1">Confirma tu contraseña</h2>
    <p class="text-sm text-[var(--text-muted)] mb-6">
        Por seguridad, confirma tu contraseña antes de continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Confirmar
        </x-primary-button>
    </form>
</x-guest-layout>
