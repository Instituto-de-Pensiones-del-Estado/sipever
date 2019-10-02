<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProcedimientosAlmacenadosFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**Procedimiento almacenado para la obtención de todos los artículos de un grupo en específico.    
         * El grupo (Partida) debe ser un entero.
         */    
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_obtener_articulos_grupo;
            CREATE PROCEDURE `sp_obtener_articulos_grupo`(
                IN `grupo` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                Select cat_articulos.clave, cat_articulos.descripcion, cat_articulos.estatus, cat_grupos_almacen.descripcion as partida
                from cat_articulos 
                inner join cat_grupos_almacen on cat_grupos_almacen.id = cat_articulos.id_grupo
                where cat_grupos_almacen.id = grupo;
            END
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_almacenar_artículo;
            CREATE PROCEDURE `sp_almacenar_artículo`(
                IN `clave` INT,
                IN `descripcion` VARCHAR(191),
                IN `estatus` INT,
                IN `id_grupo` INT,
                IN `id_unidad` INT
            
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                insert into cat_articulos (clave, descripcion, estatus, id_grupo, id_unidad) values (clave, descripcion, estatus, id_grupo, id_unidad);
            END
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_actualizar_articulo;
            CREATE PROCEDURE `sp_actualizar_articulo`(
                IN `clave` INT,
                IN `descripcion` VARCHAR(191),
                IN `estatus` INT,
                IN `id_grupo` INT,
                IN `id_unidad` INT,
                IN `id` INT
            
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                update cat_articulos
                set clave = clave, descripcion = descripcion, estatus = estatus, id_grupo = id_grupo, id_unidad = id_unidad
                where cat_articulos.id = id;
            END
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_buscar_articulo_parametro;
            CREATE PROCEDURE `sp_buscar_articulo_parametro`(
                IN `articulo` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                Select cat_articulos.clave, cat_articulos.descripcion, cat_articulos.estatus, cat_grupos_almacen.descripcion as partida
                from cat_articulos 
                inner join cat_grupos_almacen on cat_grupos_almacen.id = cat_articulos.id_grupo
                where cat_articulos.descripcion like concat("%",articulo,"%");
            END
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_grupos;

            CREATE PROCEDURE `sp_get_grupos`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_grupos_almacen;
            END
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_ver_inventario_inicial_final_periodo;
            CREATE PROCEDURE `sp_ver_inventario_inicial_final_periodo`(
                IN `anio`
            )
        ');

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_articulos;

            CREATE PROCEDURE `sp_get_articulos`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_articulos;
            END
        ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_obtener_articulos_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_almacenar_artículo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_articulo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_buscar_articulo_parametro;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_grupos;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_articulos;');
    }
}