<?php

namespace Database\Seeders;

use App\Models\Acuse;
use App\Models\Incidencia;
use App\Models\NuevaTarjeta;
use Illuminate\Database\Seeder;

class AcuseDemoSeeder extends Seeder
{
    /**
     * Genera 25 acuses de PRUEBA (nombres y CURP inventados) para poder
     * probar búsqueda, incidencias y asignación de tarjetas sin esperar
     * al Excel real de producción. Bórralos cuando tengas datos reales
     * (ver instrucciones al final de este archivo).
     */
    public function run(): void
    {
        $nombres = ['MARIA', 'GUADALUPE', 'JUANA', 'ROSA', 'PATRICIA', 'VERONICA', 'LETICIA', 'MARGARITA'];
        $apellidos = ['HERNANDEZ', 'GARCIA', 'MARTINEZ', 'LOPEZ', 'GONZALEZ', 'RAMIREZ', 'PEREZ', 'SANCHEZ'];

        for ($i = 1; $i <= 25; $i++) {
            $folio = 1000 + $i;

            $acuse = Acuse::create([
                'cuarta_linea' => (string) $folio,
                'primer_nombre' => $nombres[array_rand($nombres)],
                'segundo_nombre' => null,
                'primer_apellido' => $apellidos[array_rand($apellidos)],
                'segundo_apellido' => $apellidos[array_rand($apellidos)],
                'curp' => 'DEMO' . str_pad((string) $i, 14, '0', STR_PAD_LEFT),
                'cuenta' => (string) (2236660000 + $i),
                'tarjeta' => str_pad((string) rand(1000, 9999), 4, '0'),
                'nss_issste' => null,
                'nss_issemym' => now()->subDays(rand(1, 20))->format('Y-m-d'),
                'impreso' => $i % 3 === 0,
            ]);

            // Al folio 1005 le ponemos una incidencia de ejemplo
            if ($folio === 1005) {
                Incidencia::create([
                    'folio' => $acuse->cuarta_linea,
                    'situacion' => 'Tarjeta no localizada en la caja 1',
                    'accion' => 'Verificar en inventario de stock',
                    'comentarios' => 'Registro de prueba generado por el seeder de demostración.',
                ]);
            }

            // Al folio 1010 le ponemos una tarjeta ya reasignada de ejemplo
            if ($folio === 1010) {
                NuevaTarjeta::create([
                    'folio' => $acuse->cuarta_linea,
                    'nueva_cuenta' => '2236669999',
                    'nueva_tarjeta' => '4321',
                    'telefono' => '55-0000-0000',
                    'correo_electronico' => 'demo@correo.com',
                    'motivo_reasignacion' => 'Tarjeta no localizada',
                ]);
            }
        }
    }
}
