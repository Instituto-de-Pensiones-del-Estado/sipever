<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed Roles
        $this->call(RoleTableSeeder::class);

        // Seed Usuarios
        $this->call(UserTableSeeder::class);

        // Los usuarios necesitarán los roles previamente generados
        //$this->call(CatSistemasTableSeeder::class);
        // Los usuarios necesitarán los roles previamente generados
        //$this->call(CatTipoPersonaTableSeeder::class);
        // Los usuarios necesitarán los roles previamente generados
        //$this->call(CatEmpleadosTableSeeder::class);

        // Seed Estado Civil
        $this->call(CatEstadoCivilTableSeeder::class);

        // Seed Viviendas
        $this->call(CatViviendasTableSeeder::class);

        // Seed Situaciones
        $this->call(CatSituacionesTableSeeder::class);

        // Seed Tipos Pensiones
        $this->call(CatTiposPensionesTableSeeder::class);

        // Seed Unidades de Almacén
        $this->call(CatUnidadesAlmacenTableSeeder::class);

        // Seed Proveedores
        $this->call(CatProveedoresTableSeeder::class);

        // Seed Oficinas
        $this->call(CatOficinasTableSeeder::class);

        // Seed Organismos
        //$this->call(CatOrganismosTableSeeder::class);

        // Seed Dependencias
        //$this->call(CatDependenciasTableSeeder::class);

        // Seed Cuentas Contables
        $this->call(CatCuentaContableTableSeeder::class);

        // Seed Articulos
        $this->call(CatArticulosTableSeeder::class);

        // Seed Extensiones
        //$this->call(CatExtensionesTableSeeder::class);

         // Seed Empleados
         $this->call(CatEmpleadosTableSeeder::class);


    }
}


