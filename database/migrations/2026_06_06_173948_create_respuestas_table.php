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
        Schema::create('respuestas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluacion_id')
                ->constrained('evaluaciones')
                ->cascadeOnDelete();

            $table->foreignId('pregunta_id')
                ->constrained('preguntas')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('calificacion');

            $table->timestamps();

            $table->unique(
                ['evaluacion_id', 'pregunta_id'],
                'respuesta_evaluacion_pregunta_unica'
            );
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas');
    }
};
