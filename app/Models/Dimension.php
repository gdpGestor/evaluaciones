<?php

namespace App\Models;

use App\Models\Plantilla;
use App\Models\Pregunta;
use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    protected $table = 'dimensiones';

    protected $fillable = [
        'plantilla_id',
        'nombre',
        'factor',
        'orden',
        'activo',
    ];

    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class);
    }

}
