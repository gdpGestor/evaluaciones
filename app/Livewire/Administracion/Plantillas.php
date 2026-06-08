<?php

namespace App\Livewire\Administracion;

use App\Models\Dimension;
use App\Models\Plantilla;
use App\Models\Pregunta;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Plantillas extends Component
{
    /*
     * Búsqueda del listado principal.
     */
    public string $buscar = '';

    /*
     * Plantilla abierta actualmente en el editor.
     */
    public ?int $plantillaSeleccionadaId = null;

    /*
     * Control de ventanas modales.
     */
    public bool $mostrarModalPlantilla = false;

    public bool $mostrarModalDimension = false;

    public bool $mostrarModalPregunta = false;

    /*
     * Campos del formulario de plantilla.
     */
    public ?int $plantillaId = null;

    public string $codigoPlantilla = '';

    public string $nombrePlantilla = '';

    public string $descripcionPlantilla = '';

    public bool $plantillaActiva = true;

    /*
     * Campos del formulario de dimensión.
     */
    public ?int $dimensionId = null;

    public string $nombreDimension = '';

    public string $factorDimension = '';

    public ?int $ordenDimension = null;

    public bool $dimensionActiva = true;

    /*
     * Campos del formulario de pregunta.
     */
    public ?int $preguntaId = null;

    public ?int $dimensionPreguntaId = null;

    public string $textoPregunta = '';

    public ?int $ordenPregunta = null;

    public bool $preguntaActiva = true;

    /**
     * Regresa al listado general de plantillas.
     */
    public function volverAlListado(): void
    {
        $this->plantillaSeleccionadaId = null;

        $this->limpiarFormularioPlantilla();
        $this->limpiarFormularioDimension();
        $this->limpiarFormularioPregunta();
    }

    /**
     * Abre el editor integral de una plantilla.
     */
    public function administrar(int $plantillaId): void
    {
        Plantilla::findOrFail($plantillaId);

        $this->plantillaSeleccionadaId = $plantillaId;

        $this->resetValidation();
    }

    /*
     |--------------------------------------------------------------------------
     | Plantillas
     |--------------------------------------------------------------------------
     */

    /**
     * Abre el formulario vacío para crear una plantilla.
     */
    public function crearPlantilla(): void
    {
        $this->limpiarFormularioPlantilla();

        $this->mostrarModalPlantilla = true;
    }

    /**
     * Abre el formulario para editar una plantilla existente.
     */
    public function editarPlantilla(int $plantillaId): void
    {
        $plantilla = Plantilla::findOrFail($plantillaId);

        $this->plantillaId = $plantilla->id;
        $this->codigoPlantilla = $plantilla->codigo;
        $this->nombrePlantilla = $plantilla->nombre;
        $this->descripcionPlantilla = $plantilla->descripcion ?? '';
        $this->plantillaActiva = (bool) $plantilla->activo;

        $this->resetValidation();

        $this->mostrarModalPlantilla = true;
    }

    /**
     * Crea o actualiza el encabezado de una plantilla.
     */
    public function guardarPlantilla(): void
    {
        $datos = $this->validate([
            'codigoPlantilla' => [
                'required',
                'string',
                'max:20',
                Rule::unique('plantillas', 'codigo')
                    ->ignore($this->plantillaId),
            ],
            'nombrePlantilla' => [
                'required',
                'string',
                'max:150',
            ],
            'descripcionPlantilla' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'plantillaActiva' => [
                'boolean',
            ],
        ], [
            'codigoPlantilla.required' => 'El código de la plantilla es obligatorio.',
            'codigoPlantilla.unique' => 'Ya existe una plantilla con este código.',
            'nombrePlantilla.required' => 'El nombre de la plantilla es obligatorio.',
            'descripcionPlantilla.max' => 'La descripción no puede superar los 1000 caracteres.',
        ]);

        $esEdicion = $this->plantillaId !== null;

        $plantilla = Plantilla::updateOrCreate(
            [
                'id' => $this->plantillaId,
            ],
            [
                'codigo' => mb_strtoupper(trim($datos['codigoPlantilla'])),
                'nombre' => trim($datos['nombrePlantilla']),
                'descripcion' => trim($datos['descripcionPlantilla']) ?: null,
                'activo' => $datos['plantillaActiva'],
            ]
        );

        /*
         * Al crear una plantilla nueva, abrimos directamente
         * su editor para agregar dimensiones y preguntas.
         */
        $this->plantillaSeleccionadaId = $plantilla->id;

        $this->cerrarModalPlantilla();

        session()->flash(
            'mensaje',
            $esEdicion
                ? 'La plantilla fue actualizada correctamente.'
                : 'La plantilla fue creada correctamente. Ahora puede agregar sus dimensiones.'
        );
    }

    /**
     * Activa o desactiva una plantilla.
     */
    public function cambiarEstadoPlantilla(int $plantillaId): void
    {
        $plantilla = Plantilla::findOrFail($plantillaId);

        $plantilla->update([
            'activo' => ! $plantilla->activo,
        ]);

        session()->flash(
            'mensaje',
            $plantilla->activo
                ? 'La plantilla fue activada correctamente.'
                : 'La plantilla fue desactivada correctamente.'
        );
    }

    /**
     * Cierra la ventana modal de plantilla.
     */
    public function cerrarModalPlantilla(): void
    {
        $this->mostrarModalPlantilla = false;

        $this->limpiarFormularioPlantilla();
    }

    /**
     * Limpia el formulario de plantilla.
     */
    private function limpiarFormularioPlantilla(): void
    {
        $this->reset(
            'plantillaId',
            'codigoPlantilla',
            'nombrePlantilla',
            'descripcionPlantilla',
            'plantillaActiva'
        );

        $this->plantillaActiva = true;

        $this->resetValidation();
    }

    /*
     |--------------------------------------------------------------------------
     | Dimensiones
     |--------------------------------------------------------------------------
     */

    /**
     * Abre el formulario vacío para agregar una dimensión
     * a la plantilla seleccionada.
     */
    public function crearDimension(): void
    {
        abort_unless($this->plantillaSeleccionadaId, 404);

        $this->limpiarFormularioDimension();

        $this->ordenDimension = $this->siguienteOrdenDimension();

        $this->mostrarModalDimension = true;
    }

    /**
     * Abre una dimensión existente para editarla.
     */
    public function editarDimension(int $dimensionId): void
    {
        $dimension = Dimension::query()
            ->where('plantilla_id', $this->plantillaSeleccionadaId)
            ->findOrFail($dimensionId);

        $this->dimensionId = $dimension->id;
        $this->nombreDimension = $dimension->nombre;
        $this->factorDimension = $dimension->factor ?? '';
        $this->ordenDimension = $dimension->orden;
        $this->dimensionActiva = (bool) $dimension->activo;

        $this->resetValidation();

        $this->mostrarModalDimension = true;
    }

    /**
     * Crea o actualiza una dimensión.
     */
    public function guardarDimension(): void
    {
        abort_unless($this->plantillaSeleccionadaId, 404);

        $datos = $this->validate([
            'nombreDimension' => [
                'required',
                'string',
                'max:200',
            ],
            'factorDimension' => [
                'nullable',
                'string',
                'max:255',
            ],
            'ordenDimension' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('dimensiones', 'orden')
                    ->where(
                        fn ($consulta) => $consulta->where(
                            'plantilla_id',
                            $this->plantillaSeleccionadaId
                        )
                    )
                    ->ignore($this->dimensionId),
            ],
            'dimensionActiva' => [
                'boolean',
            ],
        ], [
            'nombreDimension.required' => 'El nombre de la dimensión es obligatorio.',
            'ordenDimension.required' => 'El orden de la dimensión es obligatorio.',
            'ordenDimension.integer' => 'El orden debe ser un número entero.',
            'ordenDimension.min' => 'El orden debe ser mayor o igual a 1.',
            'ordenDimension.unique' => 'Ya existe otra dimensión con este orden dentro de la plantilla.',
        ]);

        $esEdicion = $this->dimensionId !== null;

        Dimension::updateOrCreate(
            [
                'id' => $this->dimensionId,
            ],
            [
                'plantilla_id' => $this->plantillaSeleccionadaId,
                'nombre' => trim($datos['nombreDimension']),
                'factor' => trim($datos['factorDimension']) ?: null,
                'orden' => $datos['ordenDimension'],
                'activo' => $datos['dimensionActiva'],
            ]
        );

        $this->cerrarModalDimension();

        session()->flash(
            'mensaje',
            $esEdicion
                ? 'La dimensión fue actualizada correctamente.'
                : 'La dimensión fue creada correctamente.'
        );
    }

    /**
     * Activa o desactiva una dimensión.
     */
    public function cambiarEstadoDimension(int $dimensionId): void
    {
        $dimension = Dimension::query()
            ->where('plantilla_id', $this->plantillaSeleccionadaId)
            ->findOrFail($dimensionId);

        $dimension->update([
            'activo' => ! $dimension->activo,
        ]);

        session()->flash(
            'mensaje',
            $dimension->activo
                ? 'La dimensión fue activada correctamente.'
                : 'La dimensión fue desactivada correctamente.'
        );
    }

    /**
     * Cierra la ventana modal de dimensión.
     */
    public function cerrarModalDimension(): void
    {
        $this->mostrarModalDimension = false;

        $this->limpiarFormularioDimension();
    }

    /**
     * Limpia el formulario de dimensión.
     */
    private function limpiarFormularioDimension(): void
    {
        $this->reset(
            'dimensionId',
            'nombreDimension',
            'factorDimension',
            'ordenDimension',
            'dimensionActiva'
        );

        $this->dimensionActiva = true;

        $this->resetValidation();
    }

    /**
     * Obtiene automáticamente el siguiente orden disponible.
     */
    private function siguienteOrdenDimension(): int
    {
        return Dimension::query()
            ->where('plantilla_id', $this->plantillaSeleccionadaId)
            ->max('orden') + 1;
    }

    /*
     |--------------------------------------------------------------------------
     | Preguntas
     |--------------------------------------------------------------------------
     */

    /**
     * Abre el formulario vacío para agregar una pregunta.
     */
    public function crearPregunta(int $dimensionId): void
    {
        $dimension = Dimension::query()
            ->where('plantilla_id', $this->plantillaSeleccionadaId)
            ->findOrFail($dimensionId);

        $this->limpiarFormularioPregunta();

        $this->dimensionPreguntaId = $dimension->id;
        $this->ordenPregunta = $this->siguienteOrdenPregunta($dimension->id);

        $this->mostrarModalPregunta = true;
    }

    /**
     * Abre una pregunta existente para editarla.
     */
    public function editarPregunta(int $preguntaId): void
    {
        $pregunta = Pregunta::query()
            ->whereHas(
                'dimension',
                fn ($consulta) => $consulta->where(
                    'plantilla_id',
                    $this->plantillaSeleccionadaId
                )
            )
            ->findOrFail($preguntaId);

        $this->preguntaId = $pregunta->id;
        $this->dimensionPreguntaId = $pregunta->dimension_id;
        $this->textoPregunta = $pregunta->texto;
        $this->ordenPregunta = $pregunta->orden;
        $this->preguntaActiva = (bool) $pregunta->activo;

        $this->resetValidation();

        $this->mostrarModalPregunta = true;
    }

    /**
     * Crea o actualiza una pregunta.
     */
    public function guardarPregunta(): void
    {
        abort_unless($this->plantillaSeleccionadaId, 404);

        $datos = $this->validate([
            'dimensionPreguntaId' => [
                'required',
                Rule::exists('dimensiones', 'id')
                    ->where(
                        fn ($consulta) => $consulta->where(
                            'plantilla_id',
                            $this->plantillaSeleccionadaId
                        )
                    ),
            ],
            'textoPregunta' => [
                'required',
                'string',
                'max:1000',
            ],
            'ordenPregunta' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('preguntas', 'orden')
                    ->where(
                        fn ($consulta) => $consulta->where(
                            'dimension_id',
                            $this->dimensionPreguntaId
                        )
                    )
                    ->ignore($this->preguntaId),
            ],
            'preguntaActiva' => [
                'boolean',
            ],
        ], [
            'dimensionPreguntaId.required' => 'Seleccione una dimensión.',
            'dimensionPreguntaId.exists' => 'La dimensión seleccionada no es válida.',
            'textoPregunta.required' => 'El texto de la pregunta es obligatorio.',
            'textoPregunta.max' => 'La pregunta no puede superar los 1000 caracteres.',
            'ordenPregunta.required' => 'El orden de la pregunta es obligatorio.',
            'ordenPregunta.integer' => 'El orden debe ser un número entero.',
            'ordenPregunta.min' => 'El orden debe ser mayor o igual a 1.',
            'ordenPregunta.unique' => 'Ya existe otra pregunta con este orden dentro de la dimensión.',
        ]);

        $esEdicion = $this->preguntaId !== null;

        Pregunta::updateOrCreate(
            [
                'id' => $this->preguntaId,
            ],
            [
                'dimension_id' => $datos['dimensionPreguntaId'],
                'texto' => trim($datos['textoPregunta']),
                'orden' => $datos['ordenPregunta'],
                'activo' => $datos['preguntaActiva'],
            ]
        );

        $this->cerrarModalPregunta();

        session()->flash(
            'mensaje',
            $esEdicion
                ? 'La pregunta fue actualizada correctamente.'
                : 'La pregunta fue creada correctamente.'
        );
    }

    /**
     * Activa o desactiva una pregunta.
     */
    public function cambiarEstadoPregunta(int $preguntaId): void
    {
        $pregunta = Pregunta::query()
            ->whereHas(
                'dimension',
                fn ($consulta) => $consulta->where(
                    'plantilla_id',
                    $this->plantillaSeleccionadaId
                )
            )
            ->findOrFail($preguntaId);

        $pregunta->update([
            'activo' => ! $pregunta->activo,
        ]);

        session()->flash(
            'mensaje',
            $pregunta->activo
                ? 'La pregunta fue activada correctamente.'
                : 'La pregunta fue desactivada correctamente.'
        );
    }

    /**
     * Cierra la ventana modal de pregunta.
     */
    public function cerrarModalPregunta(): void
    {
        $this->mostrarModalPregunta = false;

        $this->limpiarFormularioPregunta();
    }

    /**
     * Limpia el formulario de pregunta.
     */
    private function limpiarFormularioPregunta(): void
    {
        $this->reset(
            'preguntaId',
            'dimensionPreguntaId',
            'textoPregunta',
            'ordenPregunta',
            'preguntaActiva'
        );

        $this->preguntaActiva = true;

        $this->resetValidation();
    }

    /**
     * Obtiene automáticamente el siguiente orden de pregunta.
     */
    private function siguienteOrdenPregunta(int $dimensionId): int
    {
        return Pregunta::query()
            ->where('dimension_id', $dimensionId)
            ->max('orden') + 1;
    }

    /*
     |--------------------------------------------------------------------------
     | Render
     |--------------------------------------------------------------------------
     */

    /**
     * Carga el listado general y, cuando corresponde,
     * la plantilla seleccionada con todas sus dimensiones y preguntas.
     */
    public function render()
    {
        $plantillas = Plantilla::query()
            ->with([
                'dimensiones' => fn ($consulta) => $consulta
                    ->orderBy('orden')
                    ->with([
                        'preguntas' => fn ($preguntas) => $preguntas
                            ->orderBy('orden'),
                    ]),
            ])
            ->when(
                $this->buscar,
                function ($consulta) {
                    $consulta->where(function ($subconsulta) {
                        $subconsulta
                            ->where(
                                'codigo',
                                'like',
                                '%' . $this->buscar . '%'
                            )
                            ->orWhere(
                                'nombre',
                                'like',
                                '%' . $this->buscar . '%'
                            )
                            ->orWhere(
                                'descripcion',
                                'like',
                                '%' . $this->buscar . '%'
                            );
                    });
                }
            )
            ->orderBy('codigo')
            ->get();

        $plantillaSeleccionada = $this->plantillaSeleccionadaId
            ? Plantilla::query()
                ->with([
                    'dimensiones' => fn ($consulta) => $consulta
                        ->orderBy('orden')
                        ->with([
                            'preguntas' => fn ($preguntas) => $preguntas
                                ->orderBy('orden'),
                        ]),
                ])
                ->findOrFail($this->plantillaSeleccionadaId)
            : null;

        return view(
            'livewire.administracion.plantillas',
            [
                'plantillas' => $plantillas,
                'plantillaSeleccionada' => $plantillaSeleccionada,
            ]
        );
    }
}