<?php

use Illuminate\Database\Seeder;
use App\Model\Almacen\Periodo;

class PeriodoAlmacenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $periodos = array(
			[ 'no_mes' => '6', 'anio' => '2020', 'estatus' => '1']
		);

		foreach ($periodos as $periodo) {
			Periodo::create($periodo);
		}
    }
    
}
