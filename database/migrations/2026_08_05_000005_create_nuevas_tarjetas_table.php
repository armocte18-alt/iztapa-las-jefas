<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nuevas_tarjetas', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->string('nueva_cuenta', 10);
            $table->string('nueva_tarjeta', 4);
            $table->string('telefono', 20);
            $table->string('correo_electronico');
            $table->enum('motivo_reasignacion', [
                'Tarjeta entregada a otra beneficiaria',
                'Tarjeta no localizada',
                'Tarjeta dañada',
            ]);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nuevas_tarjetas');
    }
};
