<?php

namespace Database\Seeders;

use App\Models\Dimension;
use App\Models\Plantilla;
use App\Models\Pregunta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatosInicialesSeeder extends Seeder
{
    /**
     * Carga las plantillas, dimensiones y preguntas iniciales.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->guardarPlantillaGerentes();
            $this->guardarPlantillaMandosMedios();
            $this->guardarPlantillaOperativos();
        });
    }

    /**
     * Registra una plantilla completa.
     */
    private function guardarPlantilla(
        string $codigo,
        string $nombre,
        string $descripcion,
        array $dimensiones
    ): void {
        $plantilla = Plantilla::updateOrCreate(
            [
                'codigo' => $codigo,
            ],
            [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'activo' => true,
            ]
        );

        foreach ($dimensiones as $indiceDimension => $datosDimension) {
            $ordenDimension = $indiceDimension + 1;

            $dimension = Dimension::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'orden' => $ordenDimension,
                ],
                [
                    'nombre' => $datosDimension['nombre'],
                    'factor' => $datosDimension['factor'] ?? null,
                    'activo' => true,
                ]
            );

            foreach ($datosDimension['preguntas'] as $indicePregunta => $textoPregunta) {
                Pregunta::updateOrCreate(
                    [
                        'dimension_id' => $dimension->id,
                        'orden' => $indicePregunta + 1,
                    ],
                    [
                        'texto' => $textoPregunta,
                        'activo' => true,
                    ]
                );
            }
        }
    }

    /**
     * Plantilla para evaluar puestos gerenciales.
     */
    private function guardarPlantillaGerentes(): void
    {
        $this->guardarPlantilla(
            codigo: 'GER',
            nombre: 'Plantilla para gerentes',
            descripcion: 'Evaluación del desempeño para puestos gerenciales.',
            dimensiones: [
                [
                    'nombre' => 'Desempeño orientado a resultados',
                    'factor' => 'Cumplimiento de metas y objetivos',
                    'preguntas' => [
                        'Cumple las metas comerciales y operativas establecidas para su área.',
                        'Da seguimiento oportuno a indicadores y resultados del área bajo su responsabilidad.',
                        'Implementa acciones correctivas cuando identifica desviaciones en los resultados esperados.',
                        'Prioriza actividades alineadas a los objetivos estratégicos de la empresa.',
                    ],
                ],
                [
                    'nombre' => 'Desempeño conductual',
                    'factor' => 'Liderazgo y gestión organizacional',
                    'preguntas' => [
                        'Coordina y supervisa efectivamente las actividades de su equipo de trabajo.',
                        'Mantiene comunicación clara y oportuna con colaboradores y otras áreas.',
                        'Toma decisiones oportunas ante situaciones operativas o estratégicas.',
                        'Promueve el cumplimiento de políticas y lineamientos organizacionales.',
                    ],
                ],
                [
                    'nombre' => 'Desempeño basado en competencias',
                    'factor' => 'Competencias gerenciales',
                    'preguntas' => [
                        'Demuestra capacidad de análisis para la toma de decisiones estratégicas.',
                        'Gestiona eficientemente recursos financieros, comerciales y operativos.',
                        'Desarrolla estrategias orientadas al crecimiento y rentabilidad del negocio.',
                        'Demuestra conocimiento técnico y organizacional requerido para el puesto.',
                    ],
                ],
                [
                    'nombre' => 'Desempeño contextual',
                    'factor' => 'Contribución organizacional',
                    'preguntas' => [
                        'Promueve el trabajo colaborativo entre áreas y equipos de trabajo.',
                        'Propone mejoras para optimizar procesos y resultados organizacionales.',
                        'Mantiene disposición para adaptarse a cambios y nuevos retos organizacionales.',
                        'Contribuye al fortalecimiento del clima organizacional y desarrollo del personal.',
                    ],
                ],
            ]
        );
    }

    /**
     * Plantilla para evaluar mandos medios.
     */
    private function guardarPlantillaMandosMedios(): void
    {
        $this->guardarPlantilla(
            codigo: 'MM',
            nombre: 'Plantilla para mandos medios',
            descripcion: 'Evaluación del desempeño para jefaturas y mandos medios.',
            dimensiones: $this->dimensionesPersonal()
        );
    }

    /**
     * Plantilla para evaluar personal operativo.
     */
    private function guardarPlantillaOperativos(): void
    {
        $this->guardarPlantilla(
            codigo: 'OPE',
            nombre: 'Plantilla para personal operativo',
            descripcion: 'Evaluación del desempeño para personal operativo.',
            dimensiones: $this->dimensionesPersonal()
        );
    }

    /**
     * Dimensiones compartidas inicialmente por mandos medios y operativos.
     */
    private function dimensionesPersonal(): array
    {
        return [
            [
                'nombre' => 'Comunicación y relación con el equipo',
                'preguntas' => [
                    'Comunica instrucciones y expectativas de manera clara.',
                    'Escucha las opiniones y sugerencias del equipo.',
                    'Mantiene una comunicación respetuosa y profesional.',
                    'Brinda retroalimentación oportuna sobre el trabajo realizado.',
                ],
            ],
            [
                'nombre' => 'Liderazgo y gestión del equipo',
                'preguntas' => [
                    'Organiza adecuadamente el trabajo del equipo.',
                    'Da seguimiento a las actividades y responsabilidades del área.',
                    'Apoya al equipo cuando se presentan dificultades laborales.',
                    'Promueve un ambiente de trabajo colaborativo y respetuoso.',
                ],
            ],
            [
                'nombre' => 'Apoyo y desarrollo del colaborador',
                'preguntas' => [
                    'Brinda orientación cuando se requiere apoyo en el trabajo.',
                    'Promueve el aprendizaje y desarrollo del equipo.',
                    'Reconoce el esfuerzo y desempeño de los colaboradores.',
                    'Mantiene disposición para resolver dudas o problemas.',
                ],
            ],
            [
                'nombre' => 'Compromiso y gestión organizacional',
                'preguntas' => [
                    'Actúa de forma alineada a los valores de la organización.',
                    'Toma decisiones de manera justa y objetiva.',
                    'Mantiene una actitud positiva y orientada a resultados.',
                    'Promueve el cumplimiento de objetivos y responsabilidades.',
                ],
            ],
        ];
    }
}