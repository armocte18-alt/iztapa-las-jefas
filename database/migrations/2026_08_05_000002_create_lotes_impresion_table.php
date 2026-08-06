<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_impresion', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cantidad');
            $table->string('rango_folios');
            $table->timestamp('fecha_generacion');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_impresion');
    }
};
