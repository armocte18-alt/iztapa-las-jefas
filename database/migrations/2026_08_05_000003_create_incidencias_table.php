<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();

            // unique(): un folio solo puede tener UNA incidencia activa a la vez,
            // igual que ya lo hacía tu updateOrInsert() por folio.
            $table->string('folio', 20)->unique();

            $table->string('situacion');
            $table->string('accion');
            $table->text('comentarios')->nullable();
            $table->enum('estatus', ['Pendiente', 'Atendido'])->default('Pendiente')->index();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
