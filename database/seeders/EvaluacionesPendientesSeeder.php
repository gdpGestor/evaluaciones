<?php

namespace Database\Seeders;

use App\Models\Empleado;
use App\Models\Evaluacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluacionesPendientesSeeder extends Seeder
{
    /**
     * Genera las evaluaciones pendientes entre empleados
     * relacionados directamente por su jefatura inmediata.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $empleadosConJefe = Empleado::query()
                ->with([
                    'puesto.plantilla',
                    'jefe.puesto.plantilla',
                ])
                ->whereNotNull('jefe_id')
                ->get();

            foreach ($empleadosConJefe as $subordinado) {
                $jefe = $subordinado->jefe;

                /*
                 * El jefe evalúa al subordinado.
                 * Se utiliza la plantilla asignada al puesto del subordinado.
                 */
                $this->crearEvaluacion(
                    evaluador: $jefe,
                    evaluado: $subordinado
                );

                /*
                 * El subordinado evalúa al jefe.
                 * Se utiliza la plantilla asignada al puesto del jefe.
                 */
                $this->crearEvaluacion(
                    evaluador: $subordinado,
                    evaluado: $jefe
                );
            }
        });
    }

    /**
     * Registra una evaluación pendiente sin duplicarla.
     */
    private function crearEvaluacion(
        Empleado $evaluador,
        Empleado $evaluado
    ): void {
        Evaluacion::updateOrCreate(
            [
                'empleado_evaluador_id' => $evaluador->id,
                'empleado_evaluado_id' => $evaluado->id,
            ],
            [
                'plantilla_id' => $evaluado->puesto->plantilla_id,
                'estado' => 'pendiente',
                'fecha_finalizacion' => null,
            ]
        );
    }
}