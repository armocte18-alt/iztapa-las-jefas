<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acuses', function (Blueprint $table) {
            $table->id();

            // Nombre histórico conservado a propósito: tus reportes de Excel
            // e importaciones ya usan esta columna. Es el folio del acuse.
            $table->string('cuarta_linea', 20)->unique();

            // Columna nueva: folio ya convertido a entero y con índice real.
            // Antes cada consulta hacía CAST(cuarta_linea AS UNSIGNED) al vuelo,
            // lo que evita que MySQL use un índice normal. Esta se llena sola
            // en el modelo Acuse (ver evento 'saving') y siempre queda sincronizada.
            $table->unsignedInteger('folio_numerico')->index();

            $table->string('primer_nombre', 100);
            $table->string('segundo_nombre', 100)->nullable();
            $table->string('primer_apellido', 100);
            $table->string('segundo_apellido', 100)->nullable();

            $table->string('curp', 18)->index();
            $table->string('cuenta', 10)->nullable()->index();
            $table->string('tarjeta', 4)->nullable();

            // Sufijo de folio para beneficiarias ISSSTE (ej. "- H"), tal como
            // ya lo manejabas en el PDF.
            $table->string('nss_issste', 10)->nullable();

            // Fecha de convocatoria (nombre histórico conservado).
            $table->date('nss_issemym')->nullable();

            $table->boolean('impreso')->default(false)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acuses');
    }
};
