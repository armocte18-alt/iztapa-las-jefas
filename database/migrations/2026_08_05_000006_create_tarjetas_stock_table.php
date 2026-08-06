<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjetas_stock', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->string('caja', 20)->nullable();
            $table->string('paquete', 20)->nullable();
            $table->text('comentarios')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjetas_stock');
    }
};
