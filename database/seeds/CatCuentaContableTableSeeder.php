<?php

use Illuminate\Database\Seeder;
use App\Model\Catalogos\CuentaContable;

class CatCuentaContableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cuentas = array(
            [ 'cta' => '1113', 'scta' => '2400', 'sscta' => '2471', 'nombre' => 'ARTÍCULOS METÁLICOS PARA LA CONSTRUCCIÓN', 'ctaarmo' => '1.1.5.1.1.9.3', 'nomarmo' => 'ARTÍCULOS METÁLICOS PARA LA CONSTRUCCIÓN', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2600', 'sscta' => '2611', 'nombre' => 'COMBUSTIBLES Y LUBRICANTES', 'ctaarmo' => '1.1.5.1.11.1', 'nomarmo' => 'COMBUSTIBLES Y LUBRICANTES', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2900', 'sscta' => '2911', 'nombre' => 'HERRAMIENTAS MENORES', 'ctaarmo' => '1.1.5.1.1.4.12', 'nomarmo' => 'HERRAMIENTAS MENORES', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2100', 'sscta' => '2122', 'nombre' => 'MATERIAL DE FOTOGRAFÍA', 'ctaarmo' => '1.1.5.1.1.1.6', 'nomarmo' => 'MATERIAL DE FOTOGRAFÍA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2100', 'sscta' => '2121', 'nombre' => 'MATERIAL DE IMPRENTA', 'ctaarmo' => '1.1.5.1.1.1.9', 'nomarmo' => 'MATERIAL DE IMPRENTA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2100', 'sscta' => '2161', 'nombre' => 'MATERIAL DE LIMPIEZA', 'ctaarmo' => '1.1.5.1.1.1.8', 'nomarmo' => 'MATERIAL DE LIMPIEZA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2400', 'sscta' => '2491', 'nombre' => 'MATERIAL DE PINTURA', 'ctaarmo' => '1.1.5.1.1.9.2', 'nomarmo' => 'MATERIAL DE PINTURA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2400', 'sscta' => '2461', 'nombre' => 'MATERIAL ELÉCTRICO Y ELECTRÓNICO', 'ctaarmo' => '1.1.5.1.1.9.1', 'nomarmo' => 'MATERIAL ELÉCTRICO Y ELECTRÓNICO', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2100', 'sscta' => '2141', 'nombre' => 'MATERIAL PARA COMPUTADORA', 'ctaarmo' => '1.1.5.1.1.1.7', 'nomarmo' => 'MATERIAL PARA COMPUTADORA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2900', 'sscta' => '2991', 'nombre' => 'MATERIALES Y SUMINISTROS VARIOS', 'ctaarmo' => '1.1.5.1.1.4.11', 'nomarmo' => 'MATERIALES Y SUMINISTROS VARIOS', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2700', 'sscta' => '2721', 'nombre' => 'PRENDAS DE PROTECCIÓN', 'ctaarmo' => '1.1.5.1.1.12.1', 'nomarmo' => 'PRENDAS DE PROTECCIÓN', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2700', 'sscta' => '2961', 'nombre' => 'REFACCIONES Y ACCESORIOS MENORES DE EQUIPO DE TRANSPORTE', 'ctaarmo' => '1.1.5.1.1.4.13', 'nomarmo' => 'REFACCIONES Y ACCESORIOS MENORES DE EQUIPO DE TRANSPORTE', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2700', 'sscta' => '2941', 'nombre' => 'REFACCIONES Y ACCESORIOS DE EQUIPO DE COMPUTO', 'ctaarmo' => '1.1.5.1.1.4.14', 'nomarmo' => 'REFACCIONES Y ACCESORIOS DE EQUIPO DE COMPUTO', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '3300', 'sscta' => '3361', 'nombre' => 'SERVICIO DE FOTOCOPIADO E IMPRESIÓN DE FORMA', 'ctaarmo' => '1.1.5.1.1.10.1', 'nomarmo' => 'SERVICIO DE FOTOCOPIADO E IMPRESIÓN DE FORMA', 'grupo' => 'B', 'estatus' => '1'],
            [ 'cta' => '1113', 'scta' => '2100', 'sscta' => '2111', 'nombre' => 'ÚTILES DE ESCRITORIO Y DE OFICINA', 'ctaarmo' => '1.1.5.1.1.1.5', 'nomarmo' => 'ÚTILES DE ESCRITORIO Y DE OFICINA', 'grupo' => 'B', 'estatus' => '1']
        );
        //Ciclo
        foreach ($cuentas as $cuenta) {
			CuentaContable::create($cuenta);
		}
    }
}
