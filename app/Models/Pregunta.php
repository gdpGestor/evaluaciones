<?php

namespace App\Models;

use App\Models\Dimension;
use App\Models\Respuesta;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'preguntas';

    protected $fillable = [
        'dimension_id',
        'texto',
        'orden',
        'activo',
    ];

    public function dimension()
    {
        return $this->belongsTo(Dimension::class);
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class);
    }

}
