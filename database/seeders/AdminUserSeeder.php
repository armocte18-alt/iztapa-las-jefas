<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea el primer usuario administrador si no existe ya uno.
     * IMPORTANTE: cambia el correo y la contraseña antes de correr esto,
     * y cambia la contraseña real desde el sistema en cuanto inicies sesión.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'constanza.rodriguez@finabien.gob.mx'],
            [
                'name' => 'Administrador del Sistema',
                'password' => Hash::make('IztapaLaCons'),
                'rol' => 'administrador',
                'activo' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
