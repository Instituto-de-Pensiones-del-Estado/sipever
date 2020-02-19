<?php

use Illuminate\Database\Seeder;
use App\Model\Catalogos\UnidadesAlmacen;

class CatUnidadesAlmacenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unidades = array(
			[ 'descripcion_corta' => 'PZA.', 'descripcion_larga' => 'PIEZA(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'CJA.', 'descripcion_larga' => 'CAJA(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'CTO.', 'descripcion_larga' => 'CIENTO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'BLK.', 'descripcion_larga' => 'BLOCK(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'PGO.', 'descripcion_larga' => 'PLIEGO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'MTO.', 'descripcion_larga' => 'METRO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'LTO.', 'descripcion_larga' => 'LITRO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'KLO.', 'descripcion_larga' => 'KILO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'JGO.', 'descripcion_larga' => 'JUEGO(S)', 'estatus' => '1'],
			[ 'descripcion_corta' => 'PAQ.', 'descripcion_larga' => 'PAQUETE(S)', 'estatus' => '1']
			
		);

		foreach ($unidades as $unidad) {
			UnidadesAlmacen::create($unidad);
		}
    }
}
