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

        /**
         * Procedimiento para obtener todos los proveedores
         * No recibe parámetros. Se obtienen todos los proveedores ordenados por su ID
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_proveedores;

            CREATE PROCEDURE `sp_get_proveedores`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_proveedores
                ORDER BY nombre;
            END
        ');

        /**Procedimiento almacenado para la obtención de todos los artículos en general.
         * No recibe parametros. Se obtienen todos los artículos ordenados por su ID
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_articulos;

            CREATE PROCEDURE `sp_get_articulos`(
                IN id_comienzo INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
            SELECT a.id, a.clave, a.descripcion, a.estatus, a.stock_minimo, a.existencias, FORMAT(a.precio_unitario,2) AS precio_unitario, b.descripcion_corta AS descripcion_u_medida, c.nombre as descripcion_cuenta
                FROM cat_articulos a
                INNER JOIN cat_unidades_almacen b ON a.id_unidad = b.id
                INNER JOIN cat_cuentas_contables c ON a.id_cuenta = c.id
                WHERE a.id >= id_comienzo
                LIMIT 10;
            END
        ');


        /**Procedimiento almacenado para la obtención de todos los artículos de un grupo en específico.
         * El grupo (Partida) debe ser el nombre del grupo, el nombre se selecciona automáticamente mediante un combobox.
         * Por ejemplo se selecciona la partida 'MATERIAL DE PINTURA', como resultado se obtendrán solo los datos de los artículos de la partida de 'MATERIAL DE PINTURA'
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_obtener_articulos_grupo;
            CREATE PROCEDURE `sp_obtener_articulos_grupo`(
                IN `grupo` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                Select a.id, a.clave, a.descripcion, a.estatus, a.stock_minimo, a.stock_maximo,
                        a.existencias, FORMAT(a.precio_unitario,2) AS precio_unitario, b.nombre as descripcion_cuenta, c.descripcion_corta AS descripcion_u_medida
                from cat_articulos a
                inner join cat_cuentas_contables b on b.id = a.id_cuenta
                inner join cat_unidades_almacen c on c.id = a.id_unidad
                where b.nombre = grupo;
            END
        ');

        /**Procedimiento almacenado para la obtención de todos los artículos que cumplan con un nombre en específico.
         * El artículo es una cadena de caractéres que servirán para localizar a todos los artículos que tengan dicha cadena.
         */
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
                Select a.id, a.clave, a.descripcion, a.estatus, a.stock_minimo,
                        a.existencias, FORMAT(a.precio_unitario,2) AS precio_unitario, b.nombre as descripcion_cuenta, c.descripcion_corta AS descripcion_u_medida
                from cat_articulos a
                INNER JOIN cat_cuentas_contables b ON b.id = a.id_cuenta
                INNER JOIN cat_unidades_almacen c ON c.id = a.id_unidad
                WHERE a.descripcion LIKE CONCAT("%",articulo,"%");
            END
        ');

        /**Procedimiento almacenado para agregar un nuevo artículo.
         * Recibe como parametros: la descripcion, el estatus, la clave, el stock minimo, el stock máximo, las existencias y el precio unitario.
         * Parametros como el grupo y la unidad de medida se proporcionarán con cadenas de caracteres por la selección de los combobox.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_almacenar_artículo;
            CREATE PROCEDURE `sp_almacenar_artículo`(
                IN `clave` INT,
                IN `descripcion` VARCHAR(191),
                IN `estatus` INT,
                IN `stock_minimo` INT,
                IN `stock_maximo` INT,
                IN `existencias` INT,
                IN `precio_unitario` DOUBLE,
                IN `grupo` VARCHAR(191),
                IN `unidad` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                INSERT INTO cat_articulos (clave, fecha_baja, descripcion, estatus, stock_minimo, stock_maximo, existencias, precio_unitario, id_cuenta, id_unidad, created_at)
                    values (clave, CURDATE(), descripcion, estatus, stock_minimo, stock_maximo, existencias, precio_unitario,
                    (SELECT cat_cuentas_contables.id FROM cat_cuentas_contables WHERE cat_cuentas_contables.nombre LIKE grupo),
                    (SELECT cat_unidades_almacen.id FROM cat_unidades_almacen WHERE cat_unidades_almacen.descripcion_corta LIKE unidad),
                    NOW());
            END
        ');

        /**Procedimiento almacenado para la actualización de algún artículo.
         * Recibe como parametros los mismos que al agregar uno nuevo, con la exepción de que no todos los campos se podrán editar.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_actualizar_articulo;
            CREATE PROCEDURE `sp_actualizar_articulo`(
                IN `clave` INT,
                IN `descripcion` VARCHAR(191),
                IN `estatus` INT,
                IN `existencias` INT,
                IN `precio_unitario` DOUBLE,
                IN `grupo` VARCHAR(191),
                IN `stock_minimo` INT,
                IN `unidad` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                UPDATE cat_articulos
                SET descripcion = descripcion, estatus = estatus, existencias = existencias, precio_unitario = precio_unitario,
                stock_minimo=stock_minimo,
                id_cuenta = (SELECT cat_cuentas_contables.id FROM cat_cuentas_contables WHERE cat_cuentas_contables.nombre LIKE grupo),
                id_unidad = (SELECT cat_unidades_almacen.id FROM cat_unidades_almacen WHERE cat_unidades_almacen.descripcion_corta LIKE unidad),
                updated_at = NOW()
                WHERE cat_articulos.clave = clave;
            END
        ');

        /**Procedimiento almacenado para la eliminación de algún artículo.
         * Recibe como parametro la clave del artículo que será eliminado.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_eliminar_articulo;

            CREATE PROCEDURE `sp_eliminar_articulo`(
                IN `clave` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                DELETE FROM cat_articulos WHERE cat_articulos.clave = clave;
            END
        ');

        /**Procedimiento almacenado para dar de baja algún artículo.
         * Recibe como parametro la clave del artículo que será dado de baja.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_baja_articulo;

            CREATE PROCEDURE `sp_baja_articulo`(
                IN `clave` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
               UPDATE cat_articulos
               SET estatus = 0, existencias = 0, updated_at = NOW()
               WHERE cat_articulos.clave = clave;
            END
        ');

        /**Procedimiento almacenado para la obtención de todos los grupos (Partidas)
         * No recibe parametros.
         */

        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_grupos;

            CREATE PROCEDURE `sp_get_grupos`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_cuentas_contables;
            END
        ');

        /**Procedimiento almacenado para almacenar un nuevo grupo (Partida)
         * Recibe como parametros: cta, scta, sscta, nombre, ctaarmo, nomarmo, grupo, estatus.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_almacenar_grupo;

            CREATE PROCEDURE `sp_almacenar_grupo`(
                IN `cta` CHAR(4),
                IN `scta` CHAR(4),
                IN `sscta` CHAR(4),
                IN `nombre` VARCHAR(191),
                IN `ctaarmo` VARCHAR(191),
                IN `nomarmo` VARCHAR(191),
                IN `grupo` CHAR(1),
                IN `estatus` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                INSERT INTO cat_cuentas_contables (cta, scta, sscta, nombre, ctaarmo, nomarmo, grupo, estatus, created_at)
                        VALUES (cta, scta, sscta, nombre, ctaarmo, nomarmo, grupo, estatus, NOW());
            END
        ');

        /**Procedimiento almacenado para actualizar un grupo (Partida)
         * Recibe los mismo parametros que almacenar solo que no se podrán editar todos los datos. Verificar que datos son editables.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_actualizar_grupo;

            CREATE PROCEDURE `sp_actualizar_grupo`(
                IN `id` INT,
                IN `cta` CHAR(4),
                IN `scta` CHAR(4),
                IN `sscta` CHAR(4),
                IN `nombre` VARCHAR(191),
                IN `ctaarmo` VARCHAR(191),
                IN `nomarmo` VARCHAR(191),
                IN `grupo` CHAR(1),
                IN `estatus` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                UPDATE cat_cuentas_contables SET cta = cta, scta = scta, sscta = sscta, nombre = nombre, ctaarmo = ctaarmo,
                         nomarmo = nomarmo, grupo = grupo, estatus = estatus, updated_at = NOW()
                        WHERE cat_cuentas_contables.id = id;
            END
        ');

        /**Procedimiento almacenado para eliminar un grupo (Partida)
         * Este procedimiento dejaría a algunos artículos sin grupo, por lo que se reasignarán a un grupo que se introducirá como parametro.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_eliminar_grupo;

            CREATE PROCEDURE `sp_eliminar_grupo`(
                IN `id` INT,
                IN `nuevo_grupo` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                UPDATE cat_articulos SET id_cuenta =
                (SELECT cat_cuentas_contables.id FROM cat_cuentas_contables WHERE cat_cuentas_contables.nombre LIKE nuevo_grupo), updated_at = NOW()
                WHERE cat_articulos.id_cuenta = id;

                DELETE FROM cat_cuentas_contables
                WHERE cat_cuentas_contables.id = id;
            END
        ');

        /**
         * Procedimiento almacenado para obtener todas las unidades de medida de los artículos
         * de almacén
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_unidades;

            CREATE PROCEDURE `sp_get_unidades`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_unidades_almacen;
            END
        ');

        /**Procedimiento almacenado para abrir un nuevo periodo
         * No recibe parametros, solo toma la fecha actual
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_abrir_periodo;

            CREATE PROCEDURE `sp_abrir_periodo`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @condicion := (SELECT IF ( MONTH(NOW()) = 12,1,0));

                SET @mes := 0;
                SET @anio := 0;

                IF @condicion = 1 THEN
                    SET @mes := 1;
                    SET @anio := YEAR(NOW()) + 1;
                ELSE
                    SET @mes := MONTH(NOW()) + 1;
                    SET @anio := YEAR(NOW());
                END IF;
                INSERT INTO periodos (no_mes, anio, estatus) VALUES (@mes, @anio, 1);
            END
        ');

        /**Procedimiento almacenado para el registro del inventario inicial de cada artículo.
         * Todo es automático, algunos datos se actualizarán al cerrar un periodo.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_inventario_inicial;

            CREATE PROCEDURE `sp_inventario_inicial`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                DECLARE i INT DEFAULT 1;
                WHILE i <= (SELECT cat_articulos.id FROM cat_articulos ORDER BY id DESC LIMIT 0, 1) DO
                    INSERT INTO inventario_inicial_final (id_periodo, id_articulo, cant_inicial, existencias, precio_inicial, precio_promedio, estatus, created_at)
                        VALUES (
                            (SELECT periodos.id_periodo FROM periodos WHERE estatus = 1),
                            i,
                            (SELECT cat_articulos.existencias FROM cat_articulos WHERE id = i),
                            0,
                            (SELECT cat_articulos.precio_unitario FROM cat_articulos WHERE id = i),
                            0.0,
                            1,
                            NOW()
                        );
                        SET i = i + 1;
                END WHILE;
            END
        ');

        /**Procedimiento almacenado para cerrar datos del inventario por periodo
         * No recibe parametros.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_inventario_final;

            CREATE PROCEDURE `sp_inventario_final`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                DECLARE id_referencia INT DEFAULT (SELECT periodos.id_periodo
                    FROM periodos WHERE estatus = 1);

                DECLARE i INT DEFAULT 1;

                WHILE i <= (SELECT COUNT(id_periodo) FROM inventario_inicial_final WHERE inventario_inicial_final.id_periodo = id_referencia) DO
                    UPDATE inventario_inicial_final as t1 INNER JOIN cat_articulos ON t1.id_articulo = cat_articulos.id SET t1.updated_at = NOW(),
                        t1.existencias = cat_articulos.existencias,
                        t1.estatus = cat_articulos.estatus,
                        t1.precio_promedio = cat_articulos.precio_unitario
                    WHERE t1.id_periodo = id_referencia AND cat_articulos.id = i;
                    SET i = i + 1;
                END WHILE;
            END
        ');

        /**Procedimiento almacenado para el loging de las oficinas
         * Solo recibe como parametro el login.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_oficina_login;

            CREATE PROCEDURE `sp_oficina_login`(
                IN `login` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT * FROM cat_oficinas WHERE cat_oficinas.login LIKE login;
            END
        ');

        /**Procedimiento almacenado para la población de una poliza y edición de las compras y consumos del mismo periodo.
         * Aun no se sabe que parametros podría recibir.
         * Está incompleto.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_generar_poliza;

            CREATE PROCEDURE `sp_generar_poliza`(
                IN `poliza` INT,
                IN `numero_poliza` INT,
                IN `estatus` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                INSERT INTO polizas (id_periodo, poliza, numero_poliza, estatus, created_at)
                    VALUES ((SELECT periodos.id_periodo FROM periodos WHERE periodos.estatus = 1), poliza, numero_poliza, estatus, NOW());

                UPDATE consumos SET consumos.id_poliza =
                    (SELECT polizas.id_poliza FROM polizas WHERE polizas.id_periodo =
                    (SELECT periodos.id_periodo FROM periodos WHERE periodos.estatus = 1)),
                    updated_at = NOW()
                WHERE consumos.id_periodo = (SELECT periodos.id_periodo FROM periodos WHERE periodos.estatus = 1);

                UPDATE compras SET compras.id_poliza =
                    (SELECT polizas.id_poliza FROM polizas WHERE polizas.id_periodo =
                    (SELECT periodos.id_periodo FROM periodos WHERE periodos.estatus = 1)),
                    updated_at = NOW()
                WHERE compras.id_periodo = (SELECT periodos.id_periodo FROM periodos WHERE periodos.estatus = 1);
            END
        ');

        /**Procedimiento almacenado para obtener datos de los artículos de cierto grupo.
         * Recibe como parametro el periodo en mes y año y el nombre del grupo.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_inventario_grupo;

            CREATE PROCEDURE `sp_inventario_grupo`(
                IN `anio` INT,
                IN `mes` INT,
                IN `grupo` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

	            SET @condicion1 := (SELECT IF((SELECT periodos.estatus FROM periodos WHERE periodos.no_mes = mes AND periodos.anio = anio) = 1,1,0));

                IF @condicion1 = 1 THEN
                    SELECT cuenta.nombre as grupo, articulo.descripcion as descripcion, inventario.cant_inicial as cantidad_inicial,
                        articulo.existencias as existencias_actuales, periodo.no_mes as mes, periodo.anio as anio
                    FROM inventario_inicial_final inventario
                    INNER JOIN cat_articulos articulo ON inventario.id_articulo = articulo.id
                    INNER JOIN cat_cuentas_contables cuenta ON articulo.id_cuenta = cuenta.id
                    INNER JOIN periodos periodo ON inventario.id_periodo = periodo.id_periodo
                    WHERE periodo.no_mes = mes AND periodo.anio = anio AND cuenta.nombre = grupo;
                ELSE
                    SELECT cuenta.nombre as grupo, articulo.descripcion as descripcion, inventario.cant_inicial as cantidad_inicial,
                        inventario.existencias as existencias_actuales, periodo.no_mes as mes, periodo.anio as anio
                    FROM inventario_inicial_final inventario
                    INNER JOIN cat_articulos articulo ON inventario.id_articulo = articulo.id
                    INNER JOIN cat_cuentas_contables cuenta ON articulo.id_cuenta = cuenta.id
                    INNER JOIN periodos periodo ON inventario.id_periodo = periodo.id_periodo
                    WHERE periodo.no_mes = mes AND periodo.anio = anio AND cuenta.nombre = grupo;
                END IF;
            END
        ');

        /**Procedimiento almacenado para cerrar un periodo.
         * El cierre de un periodo conlleva actualizar la información del inventario, abrir un nuevo periodo y crear nuevos datos para el inventario
         * Referente al nuevo periodo. Se crea una póliza.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_cerrar_periodo;

            CREATE PROCEDURE `sp_cerrar_periodo`(
                IN `mes` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SET @condicion1 := (SELECT IF ((SELECT periodos.estatus FROM periodos WHERE no_mes = mes AND anio = anio) = 0,0,1));
                SET @condicion2 := (SELECT IF ((SELECT COUNT(no_mes) FROM periodos WHERE no_mes = mes AND anio = anio)=0,0,1));
                SET @condicion3 := (SELECT IF (mes = (MONTH(NOW()) + 1),0,1));

                IF @condicion1 = 1 AND @condicion2 = 1 AND @condicion3 = 1 THEN
                    CALL sp_inventario_final;
                    CALL sp_generar_poliza(1,1234,1);

                    UPDATE periodos SET periodos.estatus = 0 WHERE periodos.no_mes = mes AND periodos.anio = anio;

                    CALL sp_abrir_periodo;
                    CALL sp_inventario_inicial;

                    SELECT @condicion1 + @condicion2 + @condicion3 AS result;
                ELSE
                    SELECT @condicion1 + @condicion2 + @condicion3 AS result;
                END IF;

            END;
        ');

        /**Prodedimiento almacenado para obtener todas las oficinas de cierto departamento
         * Recibe como parametro la ubpp del departamento
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_obtener_oficinas;

            CREATE PROCEDURE `sp_obtener_oficinas`(
                IN `ubpp_oficina` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT cat_oficinas.ubpp, cat_oficinas.oficina, cat_oficinas.descripcion, cat_oficinas.subdir
                FROM cat_oficinas
                WHERE oficina > 0
                AND ubpp = ubpp_oficina;
            END
        ');

        /**Prodedimiento almacenado para obtener todos los departamentos
         *
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_obtener_departamentos;

            CREATE PROCEDURE `sp_obtener_departamentos`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT cat_oficinas.ubpp, cat_oficinas.oficina, cat_oficinas.descripcion, cat_oficinas.subdir FROM cat_oficinas WHERE oficina = 0;
            END
        ');





        /** *********************************************************************VALES************************************************ */





        /**Procedimiento almacenado para crear un pedido, es el primer paso para la generación de un vale.
         * Solo recibe como parámetro el nombre de la oficina.
         * Obtiene como respuesta la clave del pedido generado.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_vale_consumo;

            CREATE PROCEDURE `sp_vale_consumo`(
                IN `descripcion_oficina` VARCHAR(191),
                IN `tipo_movimiento` INT,
                IN `folio` VARCHAR(191),
                OUT `clave` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @periodo := (SELECT id_periodo FROM periodos WHERE periodos.estatus = 1);

                INSERT INTO c_pedido_consumo (id_oficina, id_periodo, folio, tipo_movimiento, fecha_movimiento, created_at) VALUES
                    ((SELECT id FROM cat_oficinas WHERE cat_oficinas.descripcion = descripcion_oficina),
                    @periodo, folio, tipo_movimiento, CURDATE(), NOW());

                SET clave := (SELECT id_pedido_consumo FROM c_pedido_consumo WHERE c_pedido_consumo.id_pedido_consumo =
                    (SELECT MAX(id_pedido_consumo) FROM c_pedido_consumo WHERE c_pedido_consumo.id_oficina =
                    (SELECT id FROM cat_oficinas WHERE cat_oficinas.descripcion = descripcion_oficina)));
            END
        ');

        /**Procedimiento almacenado para el almacenamiento de los articulos de cada pedido. Este paso va despúes de la generación del vale
         * Recibe como parametros el id del pedido generado (obtenido del sp_vale_consumo), la clave del articulo y la cantidad solicitada
         * Se pueden almacenar diversos articulos de diversas partidas con el mismo id del pedido
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_pedido_articulos;

            CREATE PROCEDURE `sp_pedido_articulos`(
                IN `id_pedido` INT,
                IN `clave` INT,
                IN `cantidad` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                INSERT INTO d_pedido_consumo (id_pedido_consumo, id_articulo, cantidad, no_folio, created_at) VALUES
                    (id_pedido, (SELECT id FROM cat_articulos WHERE cat_articulos.clave = clave), cantidad,
                    (SELECT folio FROM c_pedido_consumo WHERE c_pedido_consumo.id_pedido_consumo = id_pedido), NOW());
            END
        ');

        /**Procedimiento almacenado que almacena el consumo hacia almacen. Es el primer paso para validar un vale
         * Recibe como parametro el id del vale generado.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_consumo;

            CREATE PROCEDURE `sp_consumo`(
                IN `id_pedido` INT,
                OUT `clave` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @periodo := (SELECT id_periodo FROM periodos WHERE periodos.estatus = 1);
                SET @oficina := (SELECT id_oficina FROM c_pedido_consumo WHERE id_pedido_consumo = id_pedido);
                SET @poliza := (SELECT id_poliza FROM polizas WHERE id_periodo = @periodo);
                SET @folio := (SELECT folio FROM c_pedido_consumo WHERE c_pedido_consumo.id_pedido_consumo = id_pedido);

                INSERT INTO consumos (id_oficina, id_poliza, id_periodo, id_pedido_consumo, folio, fecha_movimiento, created_at)
                VALUES (@oficina, @poliza, @periodo, id_pedido, @folio, NOW(), NOW());

                SET clave := (SELECT id_consumo FROM consumos WHERE consumos.id_pedido_consumo = id_pedido);
            END
        ');

        /**Procedimiento almacenado para el registro de los detalles sobre los artículos que se van a entregar. Esta es la validación del vale.
         * Recibe como parametros el id del consumo generado (obtenido del sp_consumo), la clave del artículo y la cantidad que se va a entregar.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_detalles_consumo;

            CREATE PROCEDURE `sp_detalles_consumo`(
                IN `id_consumo` INT,
                IN `clave` INT,
                IN `cantidad` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SET @id_articulo := (SELECT id FROM cat_articulos WHERE cat_articulos.clave = clave);
                SET @nombre_articulo := (SELECT descripcion FROM cat_articulos WHERE cat_articulos.id = @id_articulo);
                SET @precio := (SELECT precio_unitario FROM cat_articulos WHERE cat_articulos.id = @id_articulo);

                INSERT INTO detalles (id_consumo, id_articulo, tipo_movimiento, descripcion, cantidad, precio_unitario, subtotal, created_at)
                VALUES (id_consumo, @id_articulo, 1, @nombre_articulo, cantidad, @precio, cantidad * @precio, NOW());

                SET @existencias := (SELECT cat_articulos.existencias FROM cat_articulos WHERE cat_articulos.id = @id_articulo);

                UPDATE cat_articulos SET cat_articulos.existencias = (@existencias-cantidad) WHERE cat_articulos.id = @id_articulo;
            END
        ');

        /**Procedimiento almacenado para el almacenamiento de los articulos de cada pedido. Este paso va despúes de guardar los articulos a comprar.
         * Este es para la parte de las compras directas
         * Recibe como parametros el id del pedido generado (obtenido del sp_vale_consumo), la clave del articulo y la cantidad solicitada
         * Se pueden almacenar diversos articulos de diversas partidas con el mismo id del pedido
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_compra_articulos;

            CREATE PROCEDURE `sp_compra_articulos`(
                IN `id_pedido` INT,
                IN `descripcion` VARCHAR(191),
                IN `cantidad` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                INSERT INTO cat_articulos_compra (descripcion) VALUES (descripcion);

                INSERT INTO d_pedido_compra (id_pedido_compra, id_articulo, cantidad, no_folio, created_at) VALUES
                    (id_pedido, (SELECT MAX(id) FROM cat_articulos_compra WHERE cat_articulos_compra.descripcion = descripcion), cantidad,
                    (SELECT folio FROM c_pedido_consumo WHERE c_pedido_consumo.id_pedido_consumo = id_pedido), NOW());
            END
        ');

        /**Procedimiento almacenado para actualizar el estado genral de un vale, si se recibió o no
         *
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_actualizar_recibo_vale;

            CREATE PROCEDURE `sp_actualizar_recibo_vale`(
                IN `id_pedido` INT,
                IN `extemporaneo` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                UPDATE c_pedido_consumo SET recibido = 1, extemporaneo = extemporaneo, fecha_recepcion = NOW() WHERE c_pedido_consumo.id_pedido_consumo = id_pedido;
            END
        ');

        /**Procedimiento almacenado para los detalles de la compra
         * Recibe como parametros la clave de la compra, la descripción del artículo comprado, cantidad comprada y precio unitario.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_detalles_compra;

            CREATE PROCEDURE `sp_detalles_compra`(
                IN `clave` INT,
                IN `descripcion` VARCHAR(191),
                IN `cantidad` INT,
                IN `precio_unitario` DOUBLE
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @i := (SELECT cat_articulos.id FROM cat_articulos WHERE cat_articulos.descripcion = descripcion);
                SET @iva := (SELECT iva FROM compras WHERE compras.id_compra = clave);

                INSERT INTO detalles (id_compra, id_articulo, tipo_movimiento, descripcion, cantidad, precio_unitario, subtotal, total, created_at)
                VALUES (clave, @i, 3, descripcion, cantidad, precio_unitario, cantidad * precio_unitario,
                (((@iva / 100) * (cantidad * precio_unitario)) + (cantidad * precio_unitario)), NOW());

                SET @existencias := (SELECT cat_articulos.existencias FROM cat_articulos WHERE cat_articulos.id = @i);
                SET @precio_unitario := (SELECT cat_articulos.precio_unitario FROM cat_articulos WHERE cat_articulos.id = @i);

                UPDATE cat_articulos SET cat_articulos.precio_unitario = (((@existencias*@precio_unitario)+(cantidad*precio_unitario))/(@existencias+cantidad)),
                    cat_articulos.existencias = (@existencias+cantidad) WHERE cat_articulos.id = @i;
            END
        ');

         /**Procedimiento almacenado para el registro de una compra por artículo que esté dentro del almacén.
         * Este procedimiento afecta directamente a las existencias del artículo en el catálogo de artículos y modifica su precio conforme a la fórmula de precio promedio.
         * Recibe como parametros: mes y año para el periodo, nombre del proveedor, descripción del artículo que se se compró,
         * folio único, fecha de movimiento, número de factura, fecha de facturación, iva del producto, subtotal de la compra,
         * la cantidad que se compró del artículo y el precio unitario por el cual se compró el artículo.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_factura;

            CREATE PROCEDURE `sp_factura`(
                IN `mes` INT,
                IN `anio` INT,
                IN `nombre_proveedor` VARCHAR(191),
                IN `fecha_movimiento` DATE,
                IN `no_factura` VARCHAR(191),
                IN `fecha_facturacion` DATE,
                IN `iva` DOUBLE,
                IN `subtotal` DOUBLE,
                OUT `clave` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SET @periodo := (SELECT periodos.id_periodo FROM periodos WHERE periodos.no_mes = mes AND periodos.anio = anio AND estatus = 1);
                SET @proveedor := (SELECT cat_proveedores.id FROM cat_proveedores WHERE cat_proveedores.nombre = nombre_proveedor);

                INSERT INTO compras (id_periodo, id_proveedor, fecha_movimiento, no_factura, fecha_factura, iva, total, created_at)
                VALUES (@periodo, @proveedor, fecha_movimiento, no_factura, fecha_facturacion, iva,
                    (((iva/100)*subtotal)+subtotal), NOW());

                SET clave := (SELECT id_compra FROM compras WHERE compras.no_factura = no_factura);
            END
        ');

        /**Procedimiento almacenado para obtener todos los vales
         * No recibe parametros
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_vales;

            CREATE PROCEDURE `sp_get_vales`()
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT folio AS "folio", tipo_movimiento AS "tipo", (SELECT cat_oficinas.descripcion FROM cat_oficinas WHERE cat_oficinas.id = c_pedido_consumo.id_oficina) AS "oficina",
                    fecha_movimiento AS "fecha" FROM c_pedido_consumo
                    WHERE c_pedido_consumo.recibido = 0;
            END
        ');

        /**Procedimiento almacenado para obtener todos los articulos de un vale.
         * Recibe como parametro el folio del vale y la fecha.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_get_articulos_vale;

            CREATE PROCEDURE `sp_get_articulos_vale`(
                IN `folio` VARCHAR(200),
                IN `fecha` DATE
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @tipo_vale := (SELECT c_pedido_consumo.tipo_movimiento FROM c_pedido_consumo WHERE c_pedido_consumo.folio = folio AND c_pedido_consumo.fecha_movimiento = fecha);
                SET @periodo := (SELECT c_pedido_consumo.id_periodo FROM c_pedido_consumo WHERE c_pedido_consumo.folio = folio AND c_pedido_consumo.fecha_movimiento = fecha);

                IF @tipo_vale = 1 THEN
                    SELECT articulo.clave AS "CODIF.", articulo.descripcion AS "DESCRIPCION", unidad.descripcion_corta AS "UNIDAD", pedido.cantidad AS "CANT.", articulo.precio_unitario AS "PRECIO"
                    FROM cat_articulos articulo
                    INNER JOIN d_pedido_consumo pedido ON pedido.id_articulo = articulo.id
                    INNER JOIN cat_unidades_almacen unidad ON unidad.id = articulo.id_unidad
                    WHERE pedido.id_pedido_consumo = (SELECT c_pedido_consumo.id_pedido_consumo FROM c_pedido_consumo WHERE c_pedido_consumo.folio = folio AND c_pedido_consumo.id_periodo = @periodo);
                ELSE
                    SELECT ("N/A") AS "CODIF.", articulo.descripcion AS "DESCRIPCION", ("N/A") AS "UNIDAD", pedido.cantidad AS "CANT.", ("N/A") AS "PRECIO"
                    FROM cat_articulos_compra articulo
                    INNER JOIN d_pedido_compra pedido ON pedido.id_articulo = articulo.id
                    WHERE pedido.id_pedido_compra = (SELECT c_pedido_consumo.id_pedido_consumo FROM c_pedido_consumo WHERE c_pedido_consumo.folio = folio AND c_pedido_consumo.id_periodo = @periodo);
                END IF;
            END
        ');




        /** *******************************REPORTES********************************************* */



        /**Procedimiento almacenado para obtener el reporte "REPORTE DE CONSUMOS POR DEPARTAMENTO CORRESPONDIENTE AL MES DE X DEL X"
         * Solo obtiene los consumos de una oficina
         * Recibe como parametro la ubpp, el nombre de la oficina, el mes y el año.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_reporte_consumos_oficina;

            CREATE PROCEDURE `sp_reporte_consumos_oficina`(
                IN `ubpp` INT,
                IN `oficina` VARCHAR(191),
                IN `mes_inicio` INT,
                IN `mes_fin` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SET @mes_min := mes_inicio;
                SET @mes_max := mes_fin;
                SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                SET @aux_periodos := @periodo_min;

                myloop: WHILE IFNULL(@periodo_min, 0) = 0 DO
                    SET @mes_min := @mes_min + 1;
                    IF @mes_min > @mes_max THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                    END IF;
                END WHILE myloop;

                myloop: WHILE IFNULL(@periodo_max, 0) = 0 DO
                    SET @mes_max := @mes_max - 1;
                    IF @mes_max < @mes_min THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                    END IF;
                END WHILE myloop;

                SET @condicion1 := (SELECT IF(IFNULL(@periodo_min, 0) = 0,1,0));

                IF @condicion1 = 0 THEN

                    SET @id_oficina := (SELECT id FROM cat_oficinas WHERE cat_oficinas.ubpp = ubpp AND cat_oficinas.descripcion = oficina);
                    SET @num_cuentas := (SELECT COUNT(sscta) FROM cat_cuentas_contables);
                    SET @aux := 1;

                    WHILE @aux <= @num_cuentas DO
                        SET @condicion3 := (SELECT IF ((SELECT COUNT(articulo.clave)
                            FROM cat_articulos articulo
                            INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                            INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
                            INNER JOIN c_pedido_consumo pedido ON pedido.id_pedido_consumo = consumo.id_pedido_consumo
                            INNER JOIN periodos periodo ON periodo.id_periodo = consumo.id_periodo
                            WHERE articulo.id_cuenta = @aux AND pedido.id_oficina = @id_oficina AND consumo.id_periodo BETWEEN @periodo_min AND @periodo_max) > 0, 1, 0));

                        IF @condicion3 = 1 THEN
                            SELECT cuenta.sscta AS "PARTIDA", cuenta.nombre AS "NOMBRE" ,articulo.clave AS codificacion, articulo.descripcion AS descripcion, consumo.folio AS folio,
                                unidad.descripcion_corta AS unidad, detalle.cantidad AS cantidad, FORMAT(articulo.precio_unitario, 2) AS costo,
                                FORMAT(detalle.cantidad * articulo.precio_unitario, 2) AS importe
                            FROM consumos consumo
                            INNER JOIN detalles detalle ON detalle.id_consumo = consumo.id_consumo
                            INNER JOIN periodos periodo ON periodo.id_periodo = consumo.id_periodo
                            INNER JOIN c_pedido_consumo pedido ON pedido.id_pedido_consumo = consumo.id_pedido_consumo
                            INNER JOIN cat_articulos articulo ON articulo.id = detalle.id_articulo
                            INNER JOIN cat_unidades_almacen unidad ON unidad.id = articulo.id_unidad
                            INNER JOIN cat_cuentas_contables cuenta ON cuenta.id = articulo.id_cuenta
                            WHERE articulo.id_cuenta = @aux AND pedido.id_oficina = @id_oficina AND consumo.id_periodo BETWEEN @periodo_min AND @periodo_max;

                            SELECT COUNT(articulo.id) AS "CONSUMOS", SUM(detalle.cantidad) AS "ARTICULOS",
                                        (SELECT SUM(FORMAT(detalles.cantidad * cat_articulos.precio_unitario, 2)) FROM detalles INNER JOIN cat_articulos ON
                                        detalles.id_articulo = cat_articulos.id INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
                                        WHERE cat_articulos.id_cuenta = @aux AND consumos.id_periodo = @periodo AND consumos.id_oficina = @id_oficina) AS "IMPORTE"
                            FROM consumos consumo
                            INNER JOIN detalles detalle ON detalle.id_consumo = consumo.id_consumo
                            INNER JOIN cat_articulos articulo ON articulo.id = detalle.id_articulo
                            WHERE articulo.id_cuenta = @aux AND consumo.id_oficina = @id_oficina AND consumo.id_periodo BETWEEN @periodo_min AND @periodo_max;

                        END IF;
                        SET @aux := @aux + 1;
                    END WHILE;

                    SELECT COUNT(articulo.id) AS "CONSUMOS", SUM(detalle.cantidad) AS "ARTICULOS",
                                FORMAT((SELECT SUM(FORMAT(detalles.cantidad * cat_articulos.precio_unitario, 2)) FROM detalles INNER JOIN cat_articulos ON
                                detalles.id_articulo = cat_articulos.id INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
                                WHERE consumos.id_oficina = @id_oficina AND consumos.id_periodo BETWEEN @periodo_min AND @periodo_max),2) AS "IMPORTE "
                    FROM consumos consumo
                    INNER JOIN detalles detalle ON detalle.id_consumo = consumo.id_consumo
                    INNER JOIN cat_articulos articulo ON articulo.id = detalle.id_articulo
                    WHERE consumo.id_oficina = @id_oficina AND consumo.id_periodo BETWEEN @periodo_min AND @periodo_max;
                END IF;
            END
        ');

        /**Procedimiento almacenado para obtener el reporte "REPORTE DE CONSUMOS POR DEPARTAMENTO CORRESPONDIENTE AL MES DE X DEL X"
         * Recibe como parametros el año y el mes.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_reporte_consumos_departamento;

            CREATE PROCEDURE `sp_reporte_consumos_departamento`(
                IN `mes_inicio` INT,
                IN `mes_fin` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SET @mes_min := mes_inicio;
                SET @mes_max := mes_fin;
                SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                SET @aux_periodos := @periodo_min;

                myloop: WHILE IFNULL(@periodo_min, 0) = 0 DO
                    SET @mes_min := @mes_min + 1;
                    IF @mes_min > @mes_max THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                    END IF;
                END WHILE myloop;

                myloop: WHILE IFNULL(@periodo_max, 0) = 0 DO
                    SET @mes_max := @mes_max - 1;
                    IF @mes_max < @mes_min THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                    END IF;
                END WHILE myloop;

                SET @condicion1 := (SELECT IF(IFNULL(@periodo_min, 0) = 0,1,0));

                IF @condicion1 = 0 THEN

                    SET @ubpp := (SELECT GROUP_CONCAT(DISTINCT(ubpp)) FROM cat_oficinas);
                    SET @num_ubpp := (SELECT COUNT(DISTINCT(ubpp)) FROM cat_oficinas);
                    SET @aux_ubpp := 1;

                    WHILE @aux_ubpp <= @num_ubpp DO
                        SET @ubpp_actual := (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(@ubpp, ",", @aux_ubpp), ",", -1));
                        SET @oficina := (SELECT GROUP_CONCAT(DISTINCT(descripcion)) FROM cat_oficinas WHERE ubpp = @ubpp_actual);
                        SET @num_oficinas := (SELECT COUNT(id) FROM cat_oficinas WHERE ubpp = @ubpp_actual);
                        SET @aux_oficina := 1;

                        WHILE @aux_oficina <= @num_oficinas DO
                            SET @oficina_actual := (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(@oficina, ",", @aux_oficina), ",", -1));
                            SET @id_oficina := (SELECT id FROM cat_oficinas WHERE descripcion = @oficina_actual);
                            SET @condicion2 := (SELECT IF((SELECT COUNT(id_consumo) FROM consumos WHERE id_oficina = @id_oficina AND id_periodo BETWEEN @periodo_min AND @periodo_max) > 0, 1, 0));

                            IF @condicion2 = 1 THEN
                                SELECT @ubpp_actual AS "AREA", (SELECT descripcion FROM cat_oficinas WHERE ubpp = @ubpp_actual AND oficina = 0) AS "DEPARTAMENTO", @oficina_actual AS "OFICINA";
                                CALL sp_reporte_consumos_oficina(@ubpp_actual, @oficina_actual, @mes_min, @mes_max, anio);
                            END IF;

                            SET @aux_oficina := @aux_oficina + 1;
                        END WHILE;

                        SET @condicion3 := (SELECT IF((SELECT COUNT(id_consumo) FROM consumos
                                    INNER JOIN cat_oficinas ON cat_oficinas.id = consumos.id_oficina
                                    WHERE cat_oficinas.ubpp = @ubpp_actual AND consumos.id_periodo BETWEEN @periodo_min AND @periodo_max) > 0, 1, 0));

                        IF @condicion3 = 1 THEN
                            SELECT COUNT(articulo.id) AS "CONSUMOS", SUM(detalle.cantidad) AS "ARTICULOS",
                                            FORMAT((SELECT SUM(FORMAT(detalles.cantidad * cat_articulos.precio_unitario, 2)) FROM detalles INNER JOIN cat_articulos ON
                                            detalles.id_articulo = cat_articulos.id INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo INNER JOIN cat_oficinas
                                            ON cat_oficinas.id = consumos.id_oficina
                                            WHERE cat_oficinas.ubpp = @ubpp_actual AND consumos.id_periodo BETWEEN @periodo_min AND @periodo_max),2) AS "IMPORTE "
                                FROM consumos consumo
                                INNER JOIN detalles detalle ON detalle.id_consumo = consumo.id_consumo
                                INNER JOIN cat_oficinas oficina ON oficina.id = consumo.id_oficina
                                INNER JOIN cat_articulos articulo ON articulo.id = detalle.id_articulo
                                WHERE cat_oficinas.ubpp = @ubpp_actual AND consumos.id_periodo BETWEEN @periodo_min AND @periodo_max;
                        END IF;

                        SET @aux_ubpp := @aux_ubpp + 1;
                    END WHILE;



                END IF;
            END
        ');

        /**Procedimiento almacenado para la obtención de datos sobre el reporte "REPORTE FINAL DE EXISTENCIAS CORRESPONDIENTE AL MES DE X DEL X"
         * Recibe como parametros el mes, el año y la partida a la cual se desee obtener el reporte final de existencias.
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_reporte_final_existencias_partida;

            CREATE PROCEDURE `sp_reporte_final_existencias_partida`(
                IN `mes` INT,
                IN `anio` INT,
                IN `partida` VARCHAR(191)
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT DISTINCT(partidas.sscta) AS "SSCTA", partidas.nombre AS "PARTIDA", articulos.clave AS "CODIFICACION", articulos.descripcion AS "DESCRIPCION",
                    unidades.descripcion_corta AS "UNIDAD",
					  IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.existencias)
					  	ELSE (SELECT inventa.existencias FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
						  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					  END),0) AS "CANT.",
					  FORMAT(IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.precio_unitario)
					  	ELSE (SELECT inventa.precio_promedio FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
						  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					  END),0),2) AS "COSTO",
					  FORMAT(IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.existencias * articulos.precio_unitario)
					  	ELSE (SELECT inventa.existencias * inventa.precio_promedio FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
						  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					  END),0),2) AS "IMPORTE"
                FROM cat_articulos articulos
                INNER JOIN cat_unidades_almacen unidades ON articulos.id_unidad = unidades.id
                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id
                INNER JOIN inventario_inicial_final inventario ON articulos.id = inventario.id_articulo
                WHERE partidas.nombre = partida GROUP BY articulos.id;

                IF mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN
	                SELECT partidas.sscta AS "SSCTA", partidas.nombre AS "PARTIDA", IFNULL(COUNT(*),0) AS "CODIFICACIONES",
	                    IFNULL(SUM(articulos.existencias),0) AS "CANTIDAD DE ARTICULOS", FORMAT(IFNULL(SUM(articulos.precio_unitario * articulos.existencias),0),2) AS "SUBTOTAL"
	                FROM cat_articulos articulos
	                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id
	                WHERE partidas.nombre = partida GROUP BY articulos.id_cuenta;
                ELSE
	                SELECT partidas.sscta AS "SSCTA", partidas.nombre AS "PARTIDA", IFNULL(COUNT(*),0) AS "CODIFICACIONES",
	                    IFNULL(SUM(inventario.existencias),0) AS "CANTIDAD DE ARTICULOS", FORMAT(IFNULL(SUM(inventario.precio_promedio * inventario.existencias),0),2) AS "SUBTOTAL"
	                FROM cat_articulos articulos
	                INNER JOIN inventario_inicial_final inventario ON articulos.id = inventario.id_articulo
	                INNER JOIN periodos periodo ON inventario.id_periodo = periodo.id_periodo
	                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id
	                WHERE periodo.no_mes = mes AND periodo.anio = anio AND partidas.nombre = partida GROUP BY articulos.id_cuenta;
                END IF;

            END
        ');

        /**Procedimiento almacenado para la obtención de datos sobre el reporte "REPORTE FINAL DE EXISTENCIAS CORRESPONDIENTE AL MES DE X DEL X"
         * Recibe como parametros el mes y el año.
         * Similar al anterior solo que este obtiene la información de todas las partidas, con un concentrado final del total
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_reporte_final_existencias_todo;

            CREATE PROCEDURE `sp_reporte_final_existencias_todo`(
                IN `mes` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

                SELECT DISTINCT(partidas.sscta) AS "SSCTA", partidas.nombre AS "PARTIDA", articulos.clave AS "CODIFICACION", articulos.descripcion AS "DESCRIPCION",
                    unidades.descripcion_corta AS "UNIDAD",
					IFNULL((SELECT CASE
					    WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.existencias)
					    ELSE (SELECT inventa.existencias FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
					  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					END),0) AS "CANT.",
					FORMAT(IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.precio_unitario)
					  	ELSE (SELECT inventa.precio_promedio FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
						  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					END),0),2) AS "COSTO",
					FORMAT(IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT articulos.existencias * articulos.precio_unitario)
					  	ELSE (SELECT inventa.existencias * inventa.precio_promedio FROM inventario_inicial_final inventa INNER JOIN cat_articulos artis ON inventa.id_articulo = artis.id
						  		WHERE inventa.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio) AND artis.id = articulos.id)
					END),0),2) AS "IMPORTE"
                FROM cat_articulos articulos
                INNER JOIN cat_unidades_almacen unidades ON articulos.id_unidad = unidades.id
                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id
                INNER JOIN inventario_inicial_final inventario ON articulos.id = inventario.id_articulo
                GROUP BY articulos.id ORDER BY partidas.sscta ASC, articulos.descripcion ASC;

                IF mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN
	                SELECT partidas.sscta AS "SSCTA", partidas.nombre AS "PARTIDA", IFNULL(COUNT(*),0) AS "CODIFICACIONES",
	                    IFNULL(SUM(articulos.existencias),0) AS "CANTIDAD DE ARTICULOS", FORMAT(IFNULL(SUM(articulos.precio_unitario * articulos.existencias),0),2) AS "SUBTOTAL"
	                FROM cat_articulos articulos
	                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id GROUP BY articulos.id_cuenta ORDER BY partidas.sscta;
                ELSE
	                SELECT partidas.sscta AS "SSCTA", partidas.nombre AS "PARTIDA", IFNULL(COUNT(*),0) AS "CODIFICACIONES",
	                    IFNULL(SUM(inventario.existencias),0) AS "CANTIDAD DE ARTICULOS", FORMAT(IFNULL(SUM(inventario.precio_promedio * inventario.existencias),0),2) AS "SUBTOTAL"
	                FROM cat_articulos articulos
	                INNER JOIN inventario_inicial_final inventario ON articulos.id = inventario.id_articulo
	                INNER JOIN periodos periodo ON inventario.id_periodo = periodo.id_periodo
	                INNER JOIN cat_cuentas_contables partidas ON articulos.id_cuenta = partidas.id
	                WHERE periodo.no_mes = mes AND periodo.anio = anio GROUP BY articulos.id_cuenta ORDER BY partidas.sscta;
                END IF;

            END
        ');

        /**Procedimiento almacenado para el reporte "CONCENTRADO DE EXISTENCIAS POR ARTICULOS DEL MES DE X AL MES DE X DEL X"
         * Recibe como parametros el mes de inicio, el mes de fin y el año
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_concentrado_existencias;

            CREATE PROCEDURE `sp_concentrado_existencias`(
                IN `mes_inicio` INT,
                IN `mes_fin` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT tb1.sscta AS "SSCTA", tb1.partida AS "PARTIDA", tb1.cod AS "CODIF.", tb1.descripcion AS "DESCRIPCION", tb1.unidad AS "UNIDAD", tb2.ene AS "ENE", tb3.feb AS "FEB",
                    tb4.mar AS "MAR", tb5.abr AS "ABR", tb6.may AS "MAY", tb7.jun AS "JUN", tb8.jul AS "JUL", tb9.agos AS "AGOS", tb10.sept AS "SEPT", tb11.octu AS "OCT", tb12.nov AS "NOV", tb13.dic AS "DIC"
                FROM
                (
                    SELECT cat_cuentas_contables.sscta AS sscta, cat_cuentas_contables.nombre AS partida, cat_articulos.clave AS cod, cat_articulos.descripcion AS descripcion,
                        cat_unidades_almacen.descripcion_corta AS unidad
                    FROM cat_articulos
                    INNER JOIN cat_unidades_almacen ON cat_articulos.id_unidad = cat_unidades_almacen.id
                    INNER JOIN cat_cuentas_contables ON cat_cuentas_contables.id = cat_articulos.id_cuenta
                )tb1 INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (1 >= mes_inicio AND 1 <= mes_fin) AND 1 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (1 >= mes_inicio AND 1 <= mes_fin) AND 1 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 1 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS ene
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb2 ON tb1.cod = tb2.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (2 >= mes_inicio AND 2 <= mes_fin) AND 2 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (2 >= mes_inicio AND 2 <= mes_fin) AND 2 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 2 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS feb
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb3 ON tb1.cod = tb3.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (3 >= mes_inicio AND 3 <= mes_fin) AND 3 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (3 >= mes_inicio AND 3 <= mes_fin) AND 3 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 3 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS mar
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb4 ON tb1.cod = tb4.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (4 >= mes_inicio AND 4 <= mes_fin) AND 4 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (4 >= mes_inicio AND 4 <= mes_fin) AND 4 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 4 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS abr
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb5 ON tb1.cod = tb5.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (5 >= mes_inicio AND 5 <= mes_fin) AND 5 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (5 >= mes_inicio AND 5 <= mes_fin) AND 5 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 5 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS may
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb6 ON tb1.cod = tb6.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (6 >= mes_inicio AND 6 <= mes_fin) AND 6 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (6 >= mes_inicio AND 6 <= mes_fin) AND 6 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 6 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS jun
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb7 ON tb1.cod = tb7.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (7 >= mes_inicio AND 7 <= mes_fin) AND 7 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (7 >= mes_inicio AND 7 <= mes_fin) AND 7 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 7 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS jul
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb8 ON tb1.cod = tb8.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (8 >= mes_inicio AND 8 <= mes_fin) AND 8 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (8 >= mes_inicio AND 8 <= mes_fin) AND 8 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 8 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS agos
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb9 ON tb1.cod = tb9.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (9 >= mes_inicio AND 9 <= mes_fin) AND 9 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (9 >= mes_inicio AND 9 <= mes_fin) AND 9 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 9 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS sept
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb10 ON tb1.cod = tb10.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (10 >= mes_inicio AND 10 <= mes_fin) AND 10 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (10 >= mes_inicio AND 10 <= mes_fin) AND 10 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 10 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS octu
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb11 ON tb1.cod = tb11.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (11 >= mes_inicio AND 11 <= mes_fin) AND 11 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (11 >= mes_inicio AND 11 <= mes_fin) AND 11 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 11 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS nov
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb12 ON tb1.cod = tb12.clave INNER JOIN
                (
                    SELECT DISTINCT(articulo.clave) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (12 >= mes_inicio AND 12 <= mes_fin) AND 12 = MONTH(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE articulo.id = arti.id)
                            WHEN (12 >= mes_inicio AND 12 <= mes_fin) AND 12 != MONTH(NOW()) THEN (SELECT invi.existencias FROM inventario_inicial_final invi WHERE invi.id_periodo =
                                (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 12 AND periodos.anio = anio) AND invi.id_articulo = inv.id_articulo)
                            ELSE 0
                        END),0) AS dic
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb13 ON tb1.cod = tb13.clave
                ORDER BY tb1.sscta ASC, tb1.descripcion ASC;



                SELECT DISTINCT(tb1.cod) AS "SSCTA", tb1.partida AS "PARTIDA", tb2.ene AS "ENE", tb3.feb AS "FEB",
                    tb4.mar AS "MAR", tb5.abr AS "ABR", tb6.may AS "MAY", tb7.jun AS "JUN", tb8.jul AS "JUL", tb9.agos AS "AGOS", tb10.sept AS "SEPT", tb11.octu AS "OCT", tb12.nov AS "NOV", tb13.dic AS "DIC"
                FROM
                (
                    SELECT cat_cuentas_contables.sscta AS cod, cat_cuentas_contables.nombre AS partida
                    FROM cat_articulos
                    INNER JOIN cat_unidades_almacen ON cat_articulos.id_unidad = cat_unidades_almacen.id
                    INNER JOIN cat_cuentas_contables ON cat_cuentas_contables.id = cat_articulos.id_cuenta
                )tb1 INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (1 >= mes_inicio AND 1 <= mes_fin) AND 1 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (1 >= mes_inicio AND 1 <= mes_fin) AND 1 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 1 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS ene
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb2 ON tb1.cod = tb2.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (2 >= mes_inicio AND 2 <= mes_fin) AND 2 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (2 >= mes_inicio AND 2 <= mes_fin) AND 2 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 2 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS feb
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb3 ON tb1.cod = tb3.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (3 >= mes_inicio AND 3 <= mes_fin) AND 3 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (3 >= mes_inicio AND 3 <= mes_fin) AND 3 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 3 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS mar
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb4 ON tb1.cod = tb4.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (4 >= mes_inicio AND 4 <= mes_fin) AND 4 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (4 >= mes_inicio AND 4 <= mes_fin) AND 4 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 4 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS abr
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb5 ON tb1.cod = tb5.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (5 >= mes_inicio AND 5 <= mes_fin) AND 5 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (5 >= mes_inicio AND 5 <= mes_fin) AND 5 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 5 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS may
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb6 ON tb1.cod = tb6.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (6 >= mes_inicio AND 6 <= mes_fin) AND 6 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (6 >= mes_inicio AND 6 <= mes_fin) AND 6 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 6 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS jun
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb7 ON tb1.cod = tb7.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (7 >= mes_inicio AND 7 <= mes_fin) AND 7 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (7 >= mes_inicio AND 7 <= mes_fin) AND 7 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 7 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS jul
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb8 ON tb1.cod = tb8.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (8 >= mes_inicio AND 8 <= mes_fin) AND 8 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (8 >= mes_inicio AND 8 <= mes_fin) AND 8 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 8 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS agos
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb9 ON tb1.cod = tb9.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (9 >= mes_inicio AND 9 <= mes_fin) AND 9 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (9 >= mes_inicio AND 9 <= mes_fin) AND 9 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 9 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS sept
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb10 ON tb1.cod = tb10.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (10 >= mes_inicio AND 10 <= mes_fin) AND 10 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (10 >= mes_inicio AND 10 <= mes_fin) AND 10 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 10 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS octu
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb11 ON tb1.cod = tb11.clave INNER JOIN
                (
                SELECT DISTINCT(partida.sscta) AS clave, partida.nombre, articulo.descripcion,
                        IFNULL((SELECT CASE
                            WHEN (11 >= mes_inicio AND 11 <= mes_fin) AND 11 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (11 >= mes_inicio AND 11 <= mes_fin) AND 11 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 11 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS nov
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb12 ON tb1.cod = tb12.clave INNER JOIN
                (
                    SELECT DISTINCT(partida.sscta) AS clave,
                        IFNULL((SELECT CASE
                            WHEN (12 >= mes_inicio AND 12 <= mes_fin) AND 12 = MONTH(NOW()) THEN (SELECT SUM(arti.existencias) FROM cat_articulos arti WHERE partida.id = arti.id_cuenta)
                            WHEN (12 >= mes_inicio AND 12 <= mes_fin) AND 12 != MONTH(NOW()) THEN (SELECT SUM(invi.existencias) FROM inventario_inicial_final invi
                                INNER JOIN cat_articulos arti ON arti.id = invi.id_articulo WHERE invi.id_periodo = (SELECT id_periodo FROM periodos
                                WHERE periodos.no_mes = 12 AND periodos.anio = anio) AND partida.id = arti.id_cuenta)
                            ELSE 0
                        END),0) AS dic
                    FROM cat_articulos articulo
                    INNER JOIN cat_unidades_almacen unidad ON articulo.id_unidad = unidad.id
                    INNER JOIN inventario_inicial_final inv ON articulo.id = inv.id_articulo
                    INNER JOIN cat_cuentas_contables partida ON articulo.id_cuenta = partida.id
                )tb13 ON tb1.cod = tb13.clave
                ORDER BY tb1.cod ASC;
            END
        ');

        /**Procedimiento almacenado para la obtención del reporte "CONCENTRADO DE CONSUMOS POR ARTICULO DEL MES DE X AL MES DE X
         * DEL X"
         * Recibe como parametros los meses que e desean obtener (ej. del 1 al 6) y el año.
         *
         * Checar el UNION para combinar los resultados
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_concentrado_consumos_articulo;

            CREATE PROCEDURE `sp_concentrado_consumos_articulo`(
                IN `mes_inicio` INT,
                IN `mes_fin` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN

				SET @mes_min := mes_inicio;
                SET @mes_max := mes_fin;
                SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);

                myloop: WHILE IFNULL(@periodo_min, 0) = 0 DO
                    SET @mes_min := @mes_min + 1;
                    IF @mes_min > @mes_max THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                    END IF;
                END WHILE myloop;

                myloop: WHILE IFNULL(@periodo_max, 0) = 0 DO
                    SET @mes_max := @mes_max - 1;
                    IF @mes_max < @mes_min THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                    END IF;
                END WHILE myloop;

                    SELECT DISTINCT(tb1.clave) AS "CODIF.", tb1.descripcion AS "DESCRIPCION", tb1.descripcion_corta AS "UNIDAD", tb2.ene AS "ENE.", tb3.feb AS "FEB", tb4.mar AS "MAR", tb5.abr AS "ABR",
                    	tb6.may AS "MAY", tb7.jun AS "JUN", tb8.jul AS "JUL", tb9.agos AS "AGOS", tb10.sept AS "SEPT", tb11.octu AS "OCT", tb12.nov AS "NOV", tb13.dic AS "DIC", tb14.total AS "TOTAL"
                    FROM (
						  	SELECT articulo.clave AS clave, articulo.descripcion AS descripcion, unidad.descripcion_corta AS unidad
						  	FROM cat_articulos articulo
	                  INNER JOIN cat_unidades_almacen unidad ON unidad.id = articulo.id_unidad
	                  INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
	                  INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb1 INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (1 >= mes_inicio AND 1 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 1 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS ene
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb2 ON tb1.clave = tb2.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (2 >= mes_inicio AND 2 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 2 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS feb
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb3 ON tb1.clave = tb3.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (3 >= mes_inicio AND 3 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 3 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS mar
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb4 ON tb1.clave = tb4.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (4 >= mes_inicio AND 4 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 4 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS abr
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb5 ON tb1.clave = tb5.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (5 >= mes_inicio AND 5 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 5 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS may
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb6 ON tb1.clave = tb6.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (6 >= mes_inicio AND 6 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 6 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS jun
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb7 ON tb1.clave = tb7.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (7 >= mes_inicio AND 7 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 7 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS jul
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb8 ON tb1.clave = tb8.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (8 >= mes_inicio AND 8 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 8 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS agos
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb9 ON tb1.clave = tb9.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (9 >= mes_inicio AND 9 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 9 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS sept
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb10 ON tb1.clave = tb10.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (10 >= mes_inicio AND 10 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 10 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS octu
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb11 ON tb1.clave = tb11.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (11 >= mes_inicio AND 11 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 11 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS nov
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb12 ON tb1.clave = tb12.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, IFNULL((SELECT CASE
							  	WHEN (12 >= mes_inicio AND 12 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                           AND consumo.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 12 AND periodos.anio = anio))
							  	ELSE 0
							  END),0) AS dic
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb13 ON tb1.clave = tb13.cod INNER JOIN
						  (
						  	SELECT DISTINCT(articulo.clave) AS cod, (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                        AND consumo.id_periodo BETWEEN @periodo_min AND @periodo_max) AS total
                     FROM cat_articulos articulo
                     INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                     INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
						  )tb14 ON tb1.clave = tb14.cod
						  ORDER BY tb1.descripcion ASC;

						  SET @primer_semestre := 0;
                    SET @segundo_semestre := 0;
                    SET @mes_aux := 1;

                    WHILE @mes_aux <=6 DO
                        IF (@mes_aux + 6) >= mes_inicio AND (@mes_aux + 6) <= mes_fin THEN
                        SET @primer_semestre := @primer_semestre + IFNULL((SELECT SUM(detalles.cantidad)
                            FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
                            WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = @mes_aux AND anio = anio)),0);
                     	END IF;

                        IF (@mes_aux + 6) >= mes_inicio AND (@mes_aux + 6) <= mes_fin THEN
                        SET @segundo_semestre := @segundo_semestre + IFNULL((SELECT SUM(detalles.cantidad)
                            FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
                            WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = @mes_aux + 6 AND anio = anio)),0);
                     	END IF;
                        SET @mes_aux := @mes_aux + 1;
                    END WHILE;

            SELECT @num_articulos_consumidos AS "TIPOS DE ARTICULOS", @primer_semestre AS "TOTAL DEL PRIMER SEMESTRE",
    		  	@segundo_semestre AS "TOTAL DEL SEGUNDO SEMESTRE",
    			  IFNULL((SELECT CASE
    			  	WHEN (1 >= mes_inicio AND 1 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 1 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "ENE",
    			  IFNULL((SELECT CASE
    			  	WHEN (2 >= mes_inicio AND 2 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 2 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "FEB",
    			  IFNULL((SELECT CASE
    			  	WHEN (3 >= mes_inicio AND 3 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 3 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "MAR",
    			  IFNULL((SELECT CASE
    			  	WHEN (4 >= mes_inicio AND 4 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 4 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "ABR",
    			  IFNULL((SELECT CASE
    			  	WHEN (5 >= mes_inicio AND 5 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 5 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "MAY",
    			  IFNULL((SELECT CASE
    			  	WHEN (6 >= mes_inicio AND 6 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 6 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "JUN",
    			  IFNULL((SELECT CASE
    			  	WHEN (7 >= mes_inicio AND 7 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 7 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "JUL",
    			  IFNULL((SELECT CASE
    			  	WHEN (8 >= mes_inicio AND 8 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 8 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "AGOS",
    			  IFNULL((SELECT CASE
    			  	WHEN (9 >= mes_inicio AND 9 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 9 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "SEPT",
    			  IFNULL((SELECT CASE
    			  	WHEN (10 >= mes_inicio AND 10 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 10 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "OCT",
    			  IFNULL((SELECT CASE
    			  	WHEN (11 >= mes_inicio AND 11 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 11 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "NOV",
    			  IFNULL((SELECT CASE
    			  	WHEN (12 >= mes_inicio AND 12 <= mes_fin) THEN (SELECT SUM(detalles.cantidad) FROM detalles INNER JOIN consumos ON consumos.id_consumo = detalles.id_consumo
    				  WHERE consumos.id_periodo = (SELECT id_periodo FROM periodos WHERE periodos.no_mes = 12 AND periodos.anio = anio))
    			  	ELSE 0
    			  END),0) AS "DIC",
    			  FORMAT(SUM(@primer_semestre + @segundo_semestre),2) AS "TOTAL";

            END
        ');

        /**Procedimiento almacenado para obtnere el reporte "RELACION DE CONSUMOS POR ARTICULO CORRESPONDIENTE AL MES DE X DEL X"
         * Solo recibe como parametros el mes y el año
         * Falta terminar la tabla final, el concentrado total final está hecho, pero el concentrado total individual falta
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_relacion_consumos_articulo;

            CREATE PROCEDURE `sp_relacion_consumos_articulo`(
                IN `mes` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @periodo := (SELECT id_periodo FROM periodos WHERE periodos.no_mes = mes AND periodos.anio = anio);

                SELECT articulo.clave AS "COD.", articulo.descripcion AS "DESCRIPCION", consumo.folio AS "VALE", unidad.descripcion_corta AS "UNIDAD",
                        detalle.cantidad AS "CANT.", FORMAT(articulo.precio_unitario,2) AS "COSTO UNIT.", FORMAT((detalle.cantidad * articulo.precio_unitario),2) AS "IMPORTE",
                        (SELECT cat_oficinas.descripcion FROM cat_oficinas WHERE cat_oficinas.oficina = 0 AND cat_oficinas.ubpp = (SELECT cat_oficinas.ubpp FROM cat_oficinas
                                WHERE consumo.id_oficina = cat_oficinas.id)) AS "DEPARTAMENTO",
                        (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id AND consumo.id_consumo = detalles.id_consumo) AS "TOTAL DE ARTICULOS",
                        FORMAT((SELECT SUM(detalles.cantidad * articulo.precio_unitario) FROM detalles WHERE detalles.id_articulo = articulo.id),2) AS "TOTAL"
                FROM cat_articulos articulo
                INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
                INNER JOIN cat_oficinas oficina ON oficina.id = consumo.id_oficina
                INNER JOIN cat_unidades_almacen unidad ON unidad.id = articulo.id_unidad
                WHERE consumo.id_periodo = @periodo ORDER BY articulo.descripcion ASC, oficina.descripcion ASC;

                SELECT partida.sscta AS "SSCTA", partida.nombre AS "PARTIDA", COUNT(detalle.id_consumo) AS "CONSUMOS", SUM(detalle.cantidad) AS "CANTIDAD DE ARTICULOS",
							FORMAT(SUM(detalle.cantidad * articulo.precio_unitario),2) AS "IMPORTES TOTALES"
                FROM cat_cuentas_contables partida
					 INNER JOIN cat_articulos articulo ON articulo.id_cuenta  = partida.id
					 INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
					 INNER JOIN consumos consumo ON consumo.id_consumo = detalle.id_consumo
					 WHERE consumo.id_periodo = @periodo
					 GROUP BY partida.id
					 ORDER BY partida.sscta;
            END
        ');

        /**Procedimiento almacenado para obtener le reporte "REPORTE AUXILIAR DE ALMACEN GENERAL CORRESPONDIENTE AL MES DE X DEL X"
         * Recibe como parametros el mes y el año
         * El resultado dependerá del periodo seleccionado, si el periodo está abierto los datos se tomarán del catalogo de articulos,
         * si el periodo está cerrado los datos se tomarán del inventario inicial final
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_reporte_auxiliar_almacen;

            CREATE PROCEDURE `sp_reporte_auxiliar_almacen`(
                IN `mes` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SELECT tb1.sscta AS "SSCTA", tb1.nombre AS "PARTIDA", tb2.clave AS "COD.", tb2.descripcion AS "DESCRIPCION", tb2.unidad AS "UNIDAD", tb2.canti AS "CANT.",
					  	FORMAT(tb2.costo,2) AS "COSTO UNIT.", FORMAT(tb2.importe,2) AS "IMPORTE", FORMAT(tb2.inversion,2) AS "INV. FIN." FROM
					  (
					  	SELECT cat_cuentas_contables.sscta AS sscta, cat_cuentas_contables.nombre AS nombre, cat_cuentas_contables.id AS cod
					  	FROM cat_cuentas_contables
					  )tb1 INNER JOIN
					  (
					  	SELECT DISTINCT(articulo.clave) AS clave, articulo.id_cuenta AS cod, articulo.descripcion AS descripcion, unidad.descripcion_corta AS unidad,
					  		IFNULL((SELECT CASE
							  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT arti.existencias FROM cat_articulos arti WHERE arti.id = articulo.id)
							  	ELSE (SELECT inven.existencias FROM inventario_inicial_final inven INNER JOIN cat_articulos ON cat_articulos.id = inven.id_articulo
								WHERE inven.id_articulo = articulo.id AND inven.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio))
							END),0) AS canti,
							IFNULL((SELECT CASE
								WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT arti.precio_unitario FROM cat_articulos arti WHERE arti.id = articulo.id)
								ELSE (SELECT inven.precio_promedio FROM inventario_inicial_final inven INNER JOIN cat_articulos ON cat_articulos.id = inven.id_articulo
								WHERE inven.id_articulo = articulo.id AND inven.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio))
							END),0) AS costo,
							IFNULL((SELECT CASE
								WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT arti.existencias * arti.precio_unitario FROM cat_articulos arti WHERE arti.id = articulo.id)
								ELSE (SELECT inven.precio_promedio * inven.existencias FROM inventario_inicial_final inven INNER JOIN cat_articulos ON cat_articulos.id = inven.id_articulo
								WHERE inven.id_articulo = articulo.id AND inven.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio))
							END),0) AS importe,
							IFNULL((SELECT CASE
								WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT arti.existencias * arti.precio_unitario FROM cat_articulos arti WHERE arti.id = articulo.id)
								ELSE (SELECT inven.precio_promedio * inven.existencias FROM inventario_inicial_final inven INNER JOIN cat_articulos ON cat_articulos.id = inven.id_articulo
								WHERE inven.id_articulo = articulo.id AND inven.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio))
							END),0) AS inversion
                 FROM cat_articulos articulo
                 INNER JOIN inventario_inicial_final inventario ON inventario.id_articulo = articulo.id
                 INNER JOIN cat_unidades_almacen unidad ON unidad.id = articulo.id_unidad
                 INNER JOIN cat_cuentas_contables cuenta ON cuenta.id = articulo.id_cuenta
					  )tb2 ON tb1.cod = tb2.cod
					  ORDER BY tb1.sscta ASC, tb2.descripcion ASC;

				  SELECT tb1.articulos AS "ARTICULOS", FORMAT(tb1.importes,2) AS "IMPORTES", FORMAT(tb1.invent,2) AS "INVENTARIO" FROM
				  (
				  	  SELECT IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT SUM(articulo.existencias) FROM cat_articulos articulo)
					  	ELSE (SELECT SUM(inventario.existencias) FROM inventario_inicial_final inventario WHERE inventario.id_periodo = (SELECT id_periodo FROM periodos WHERE no_mes = mes AND anio = anio))
					  END),0) AS articulos,
					  IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT SUM(articulo.existencias * articulo.precio_unitario) FROM cat_articulos articulo)
					  	ELSE (SELECT SUM(inventario.existencias * inventario.precio_promedio) FROM inventario_inicial_final inventario WHERE inventario.id_periodo = (SELECT id_periodo FROM periodos
						  WHERE no_mes = mes AND anio = anio))
					  END),0) AS importes,
					  IFNULL((SELECT CASE
					  	WHEN mes = MONTH(NOW()) AND anio = YEAR(NOW()) THEN (SELECT SUM(articulo.existencias * articulo.precio_unitario) FROM cat_articulos articulo)
					  	ELSE (SELECT SUM(inventario.existencias * inventario.precio_promedio) FROM inventario_inicial_final inventario WHERE inventario.id_periodo = (SELECT id_periodo FROM periodos
						  WHERE no_mes = mes AND anio = anio))
					  END),0) AS invent
				  )tb1;
            END
        ');

        /**Procedimiento almacenado para obtner el reporte "CONCENTRADO DE COMPRAS POR ARTICULO X SEMESTRE: DEL MES X AL MES X DEL X"
         * Recibe como parametros los meses de inicio y fin y el año
         */
        DB::unprepared('
            DROP PROCEDURE IF EXISTS sp_concentrado_compras;

            CREATE PROCEDURE `sp_concentrado_compras`(
                IN `mes_inicio` INT,
                IN `mes_fin` INT,
                IN `anio` INT
            )
            LANGUAGE SQL
            NOT DETERMINISTIC
            CONTAINS SQL
            SQL SECURITY DEFINER
            BEGIN
                SET @mes_min := mes_inicio;
                SET @mes_max := mes_fin;
                SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);

                myloop: WHILE IFNULL(@periodo_min, 0) = 0 DO
                    SET @mes_min := @mes_min + 1;
                    IF @mes_min > @mes_max THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_min := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_min AND anio = anio);
                    END IF;
                END WHILE myloop;

                myloop: WHILE IFNULL(@periodo_max, 0) = 0 DO
                    SET @mes_max := @mes_max - 1;
                    IF @mes_max < @mes_min THEN
                        LEAVE myloop;
                    ELSE
                        SET @periodo_max := (SELECT id_periodo FROM periodos WHERE no_mes = @mes_max AND anio = anio);
                    END IF;
                END WHILE myloop;

                SET @condicion1 := (SELECT IF(IFNULL(@periodo_min, 0) = 0, 1, 0));

                IF @condicion1 = 0 THEN
                    SET @periodo_aux := @periodo_min;

                    SELECT articulo.clave AS "CODIF.", articulo.descripcion AS "DESCRIPCION"
                    FROM cat_articulos articulo
                    INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                    INNER JOIN compras compra ON compra.id_compra = detalle.id_compra
                    WHERE compra.id_periodo BETWEEN @periodo_min AND @periodo_max;

                    WHILE @periodo_aux <= @periodo_max DO
                        SELECT IFNULL((SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                        AND compra.id_periodo = @aux_periodos),0) AS "CANT.",
                        IFNULL((SELECT (detalles.cantidad * detalles.precio_unitario) FROM detalles WHERE detalles.id_articulo = articulo.id
                        AND compra.id_periodo = @aux_periodos),0) AS "IMP."
                        FROM cat_articulos articulo
                        INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                        INNER JOIN compras compra ON compra.id_compra = detalle.id_compra
                        WHERE compra.id_periodo BETWEEN @periodo_min AND @periodo_max;

                        SELECT SUM(detalles.cantidad) AS "MES CANT.", SUM(detalles.cantidad * detalles.precio_unitario) AS "MES IMP."
                        FROM detalles INNER JOIN compras ON compras.id_compra = detalles.id_compra WHERE compras.id_periodo = @periodo_aux;

                        SET @periodo_aux := @periodo_aux + 1;
                    END WHILE;

                    SELECT (SELECT SUM(detalles.cantidad) FROM detalles WHERE detalles.id_articulo = articulo.id
                    AND compra.id_periodo BETWEEN @periodo_min AND @periodo_max) AS "CANT.",
                    (SELECT SUM(detalles.cantidad * detalles.precio_unitario) FROM detalles WHERE detalles.id_articulo = articulo.id
                    AND compra.id_periodo BETWEEN @periodo_min AND @periodo_max) AS "IMP."
                    FROM cat_articulos articulo
                    INNER JOIN detalles detalle ON detalle.id_articulo = articulo.id
                    INNER JOIN compras compra ON compra.id_compra = detalle.id_compra
                    WHERE compra.id_periodo BETWEEN @periodo_min AND @periodo_max;

                    SELECT SUM(detalles.cantidad) AS "TOTAL CANT.", SUM(detalles.cantidad * detalles.precio_unitario) AS "TOTAL IMP."
                    FROM detalles INNER JOIN compras ON compras.id_compra = detalles.id_compra WHERE compras.id_periodo BETWEEN @periodo_min AND @periodo_max;
                END IF;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_proveedores;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_articulos;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_obtener_articulos_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_buscar_articulo_parametro;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_almacenar_artículo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_articulo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_eliminar_articulo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_baja_articulo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_grupos;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_almacenar_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_eliminar_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_unidades;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_abrir_periodo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_inventario_inicial;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_inventario_final;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_oficina_login;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_generar_poliza;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_inventario_grupo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_cerrar_periodo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_obtener_oficinas;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_obtener_departamentos;');

        /** *******************************************VALES************************************************* */

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_vale_consumo;');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_pedido_articulos;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_consumo;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_detalles_consumo;');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_recibo_vale;');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_compra_articulos;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_detalles_compra;');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_factura;');

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_vales;');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_articulos_vale;');

        /** *************************************REPORTES*************************************************** */

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reporte_consumos_oficina;');//Reporte - Rangos
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reporte_consumos_departamento;');//Reporte - Rangos
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reporte_final_existencias_partida;');//Reporte - Individual - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reporte_final_existencias_todo;');//Reporte - Individual - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_concentrado_existencias;');//Reporte - Rangos - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_concentrado_consumos_articulo;');//Reporte - Rangos - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_relacion_consumos_articulo;');//Reporte - Individual - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_reporte_auxiliar_almacen;');//Reporte - Individual - ☻
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_concentrado_compras;');//Reporte - Rangos
    }
}
