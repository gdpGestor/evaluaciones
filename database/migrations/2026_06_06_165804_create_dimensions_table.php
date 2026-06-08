<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dimensiones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plantilla_id')
                ->constrained('plantillas')
                ->cascadeOnDelete();

            $table->string('nombre', 200);
            $table->integer('orden')->default(1);
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimensiones');
    }
};
