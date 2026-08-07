<x-guest-layout>
    <h2 class="text-xl font-bold text-[var(--text-primary)] mb-1">Recuperar contraseña</h2>
    <p class="text-sm text-[var(--text-muted)] mb-6">
        Escribe tu correo y te enviaremos un enlace para elegir una contraseña nueva.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Enviar enlace de recuperación
        </x-primary-button>

        <p class="text-center text-sm text-[var(--text-muted)] pt-2">
            <a href="{{ route('login') }}" class="text-[#0F8A72] hover:text-[#0C6F5B] font-medium">← Volver a iniciar sesión</a>
        </p>
    </form>
</x-guest-layout>
