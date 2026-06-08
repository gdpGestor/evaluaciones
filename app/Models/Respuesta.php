<?php

namespace App\Models;

use App\Models\Evaluacion;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $table = 'respuestas';

    protected $fillable = [
        'evaluacion_id',
        'pregunta_id',
        'calificacion',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class);
    }

}
