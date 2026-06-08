<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta los seeders principales.
     */
    public function run(): void
    {
        $this->call([
            DatosInicialesSeeder::class,
            DatosOrganizacionalesSeeder::class,
            EvaluacionesPendientesSeeder::class,
        ]);

    }
}