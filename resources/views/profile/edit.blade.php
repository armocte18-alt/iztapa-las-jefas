<x-layouts.app titulo="Mi cuenta">
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="text-xl font-bold text-[var(--text-primary)]">Mi cuenta</h1>
            <p class="text-sm text-[var(--text-muted)] mt-1">Actualiza tu información y tu contraseña.</p>
        </div>

        <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl bg-[var(--bg-card)] border border-[var(--border-card)] shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl bg-[var(--bg-card)] border border-[#E4572E]/20 shadow-[0_1px_2px_rgba(16,24,40,0.05)] p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-layouts.app>
