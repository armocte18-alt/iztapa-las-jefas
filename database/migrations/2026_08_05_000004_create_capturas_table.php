<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capturas', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->string('cuenta', 10)->nullable()->index();
            $table->string('curp', 18)->nullable()->index();
            $table->date('fecha_captura')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capturas');
    }
};
