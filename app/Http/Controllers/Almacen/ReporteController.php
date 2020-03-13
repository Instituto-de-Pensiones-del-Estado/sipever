<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;

use App\Model\Catalogos\Articulo;
use App\Http\Controllers\Controller;
use Doctrine\DBAL\Driver\PDOConnection;
use Illuminate\Database\QueryException;
use Exception;
use File;
use DB;
use PDF;

use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = DB::select('SELECT DISTINCT anio FROM periodos');
        $departamentos = DB::select("call sp_obtener_departamentos");
        return view('almacen.reportes', compact('departamentos', 'years'));
    }

    /**
     * Obtiene las oficinas asociadas a un departamento
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOficinas(Request $request){
        $ubpp = $request->input('ubpp');
        $oficinas = DB::select("call sp_obtener_oficinas(?)", array($ubpp));
        return json_encode($oficinas);
    }

    /**
     * Obtiene las oficinas asociadas a un departamento
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generarReporte(Request $request){
        set_time_limit(0);
        $office_code = $request->cookie('__office_session');
        $validConsumo = $request->input('validConsumo');
        $consDepto = $request->input('consDepto');
        $consArt = $request->input('consArt');
        $reConsDepto = $request->input('reConsDepto');
        $auxAlmacen = $request->input('auxAlmacen');
        $compAlmacen = $request->input('compAlmacen');
        $simCompras = $request->input('simCompras');
        $validCompras = $request->input('validCompras');
        $validTraslados = $request->input('validTraslados');
        $consArticulo = $request->input('consArticulo');
        $existencias = $request->input('existencias');
        $compArticulo = $request->input('compArticulo');
        $existArticulo = $request->input('existArticulo');
        $consAreaArt = $request->input('consAreaArt');
        $gastoDepto = $request->input('gastoDepto');
        $consArtArea = $request->input('consArtArea');
        $numMesInicio = $request->input('numMesInicio');
        $yearInicio = $request->input('yearInicio');
        $periodo = $request->has('mesFin') && $request->has('yearFin') ? true : false;
        $mesIni = $this->nombre_mes($numMesInicio);
        $depto = $request->has('depto') ? $request->depto : null;
        $oficina = $request->has('oficina') ? $request->oficina : null;
        $ruta = "";
        $headers = [];
        $nombre_archivo="";
        $db = DB::connection()->getPdo();
        //Establecemos la conexión
        $db->setAttribute(PDOConnection::ATTR_ERRMODE, PDOConnection::ERRMODE_EXCEPTION);
        $db->setAttribute(PDOConnection::ATTR_EMULATE_PREPARES, true);
        $query = null;
        if($periodo){
            $mesFin = $request->input('mesFin');
            $yearFin = $request->input('yearFin');
            $mesF = $this->nombre_mes($mesFin);
            if($mesFin < $numMesInicio || $yearFin < $yearInicio){
                return back()->with('warning','Las fechas ingresadas no son correctas');
            }
        }
        $papel = null;
        $orientacion= null;

        if($query){
            dd('Haciendo la query');
            //Hacemos un binding de los parámetros, así protegemos nuestra
            //llamada de una posible inyección sql
            $query->bindParam(1,$numMesInicio);
            if ($periodo) {
                $query->bindParam(2,$mesFin);
            }else{
                $query->bindParam(2,$numMesInicio);
            }
            $query->bindParam(3, $yearInicio);
        }

        if($query){
            try {
                $query->execute();
                $query->closeCursor();
                //accedemos al valor de retorno para regresar la vista correspondiente.
                $results = $query->fetch(PDOConnection::FETCH_OBJ);
            } catch (Exception $e) {
                throw new QueryException("Error Processing Request", 1);
                
            }
        }

        date_default_timezone_set('America/Mexico_City');
        $fecha_nombre=date("Ymd");
        $hora_nombre=date("Hi");
        $nombre_archivo = "{$fecha_nombre}_{$nombre_archivo}_{$hora_nombre}";
        $tipo = 'reporte';
        $archivo = file_get_contents(public_path("/img_system/low_res_logo.png"));
        $imagen_b64 = base64_encode($archivo);
        $logo_b64 = "data:image/png;base64,{$imagen_b64}";
        $fecha = date("d/M/Y");
        $hora = date("h:i a");
        $pdf = null;
        /**
         * REPORTE DE VALIDACIÓN DE CONSUMOS
         */
        if ($validConsumo == "checked"){
            $mensaje = 'Reporte para validación de consumos';
            $nombre_archivo="REPVALIDCONS";
            $ruta = "almacen.reportes.reporte_validacion_cons";
            $headers = ['FOLIO.','UBPP', 'CLAVE', 'DESCRIPCIÓN', 'UNIDAD', 'CANT.', 'COSTO', 'IMPORTE'];
            $papel = 'letter';
            $orientacion='portrait';
            $consumos = DB :: table('consumos')
                ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                ->join('detalles', 'consumos.id_consumo', '=', 'detalles.id_consumo')
                ->join('cat_oficinas', 'consumos.id_oficina', '=', 'cat_oficinas.id')
                ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->select('folio','ubpp','clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'detalles.cantidad', 'detalles.precio_unitario', 'detalles.subtotal')
                ->where('periodos.estatus', '=', 1)
                ->get();

            $total_consumos = DB :: table('consumos')
                ->count('consumos.id_consumo');
            $total_arti = DB :: table ('detalles')
                ->sum('detalles.cantidad');
            $total_importe = DB :: table('detalles')
                ->sum('detalles.subtotal');          
            //Creando PDF con DOMPDF
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'consumos', 'total_consumos', 'total_arti',
                                         'total_importe', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
        }
        /**
         * REPORTE DE CONSUMOS POR DEPARTAMENTO
         */
        elseif ($consDepto == "checked") {
            $mensaje = 'Reporte de consumos por departamento';
            $nombre_archivo="REPCONSDEPTO";
            $ruta = "almacen.reportes.reporte_consumos_depto";
            $headers=['FOLIO','CODIF.','DESCRIPCION','UNIDAD','CANT.','COSTO UNIT.','IMPORTE'];
            $papel = 'letter';
            $orientacion='landscape';
            /**
             * CONSULTAS A LA BD
             * 
             * @deptos: Departamentos centrales del IPE
             * @partidas: Partidas de artículos que existen en el IPE
             * @consumos: Detalles de los consumos/vales correspondientes a un mes. Incluye: folio, partida, clave del artículo,
             * descripción, unidad de medida, cantidad, costo unitario e importe.
             * @total_consumos: Suma del total de consumos/vales en un período.
             * @total_arti: Cantidad total de artículos en un período.
             * @total_importe: Importe total de los consumos.
             */
            $deptos = DB :: table('cat_oficinas')
                ->join('consumos', 'cat_oficinas.ubpp', '=', 'consumos.ubpp_consumo')
                ->select('cat_oficinas.ubpp', 'cat_oficinas.descripcion')
                ->where('oficina', '=', 0)
                ->groupBy('ubpp', 'descripcion')
                ->get();
                //SELECT oficina, ubpp, descripcion FROM cat_oficinas INNER JOIN consumos WHERE ubpp = ubpp_consumo AND oficina = 0 GROUP BY oficina, ubpp, descripcion
            
            $partidas = DB :: table('cat_cuentas_contables')
                ->join('cat_articulos', 'cat_cuentas_contables.id', '=', 'cat_articulos.id_cuenta')
                ->join('detalles', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->select('cat_cuentas_contables.id','sscta', 'nombre')
                ->groupBy('id', 'sscta', 'nombre')
                ->get();
                //SELECT cat_cuentas_contables.id, sscta, nombre FROM cat_cuentas_contables INNER JOIN cat_articulos ON cat_cuentas_contables.id = cat_articulos.id_cuenta INNER JOIN detalles WHERE id_articulo = cat_articulos.id GROUP BY cat_cuentas_contables.id, sscta, nombre            
            
            $consumos = DB :: table('consumos')
                ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                ->join('detalles', 'consumos.id_consumo', '=', 'detalles.id_consumo')
                ->join('cat_oficinas', 'consumos.id_oficina', '=', 'cat_oficinas.id')
                ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->select('folio','ubpp','clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'detalles.cantidad', 'detalles.precio_unitario', 
                        'detalles.subtotal', 'cat_articulos.id_cuenta')
                ->where('periodos.estatus', '=', 1)
                ->get();
            //dd($partidas);
            $total_consumos = DB :: table('consumos')
                ->count('consumos.id_consumo');
            $total_articulos = DB :: table ('detalles')
                ->sum('detalles.cantidad');
            $total_importe = DB :: table('detalles')
                ->sum('detalles.subtotal');
            //dd($deptos);
            //Creando PDF con DOMPDF
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'deptos', 'partidas', 'consumos', 'total_consumos', 'total_articulos',
                                         'total_importe', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
            
        }
        /**
         * REPORTE RELACIÓN DE CONSUMOS POR ARTICULO
         */
        elseif ($consArt == "checked") {
            $mensaje = 'Reporte de consumos por artículo';
            $nombre_archivo="REPCONSART";
            $ruta = "almacen.reportes.reporte_relacion_consumos_articulo";
            $headers=['CODIF.','DESCRIPCION','VALE','UNIDAD','CANT.','COSTO UNIT.','IMPORTE', 'DEPARTAMENTO'];
            $papel = 'letter';
            $orientacion='landscape';
            /**
             * CONSULTAS A LA BD
             * @consumos_p_articulo: Refleja la estructura usada en el reporte para representar los consumos por artículos.
             * 
             * @partidas: Partidas que tuvieron consumos en el período correspondiente
             * 
             * @articulos: Artículos que tuvieron consumos en el período correspondiente
             * 
             * @total_consumos: Suma del total de consumos/vales en un período.
             * 
             * @total_articulos: Cantidad total de artículos en un período.
             * 
             * @total_importe: Importe total de los consumos.
             */

            $consumos_p_articulo = DB :: table('consumos')
                ->join('detalles', 'consumos.id_consumo', '=', 'detalles.id_consumo')
                ->join('cat_oficinas', 'consumos.id_oficina', '=', 'cat_oficinas.id')
                ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->select('clave', 'cat_articulos.descripcion as nombre', 'consumos.folio', 'detalles.cantidad', 'detalles.precio_unitario', 'detalles.subtotal',
                         'cat_oficinas.descripcion', 'cat_articulos.id_cuenta')
                ->orderBy('nombre')
                ->get();
            
            $partidas = DB :: table('cat_cuentas_contables')
                ->join('cat_articulos', 'cat_cuentas_contables.id', '=', 'cat_articulos.id_cuenta')
                ->join('detalles', 'cat_articulos.id', '=', 'detalles.id_articulo')
                ->join('consumos', 'consumos.id_consumo', '=', 'detalles.id_consumo')
                ->select('cat_cuentas_contables.id', 'sscta', 'cat_cuentas_contables.nombre')
                ->groupBy('cat_cuentas_contables.id','sscta', 'cat_cuentas_contables.nombre')
                ->get();
            
            $articulos = DB :: table('cat_articulos')
                ->join('detalles', 'cat_articulos.id', '=', 'detalles.id_articulo')
                ->join('cat_unidades_almacen', 'cat_unidades_almacen.id', '=', 'cat_articulos.id_unidad')
                ->select('clave', 'cat_articulos.descripcion as nombre', 'cat_unidades_almacen.descripcion_corta')
                ->groupBy('clave', 'nombre', 'cat_unidades_almacen.descripcion_corta')
                ->get();

            $total_consumos = DB :: table('consumos')
                ->count('consumos.id_consumo');
            $total_articulos = DB :: table ('detalles')
                ->sum('detalles.cantidad');
            $total_importe = DB :: table('detalles')
                ->sum('detalles.subtotal');

            
            //dd($consumos_p_articulo);
            //Creando PDF con DOMPDF
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'consumos_p_articulo', 'articulos', 'partidas', 'total_consumos', 
                'total_articulos', 'total_importe',  'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
            
        }
        /**
         * RESUMEN DE CONSUMOS POR DEPARTAMENTO
         */
        elseif ($reConsDepto == "checked") {
            $mensaje = 'Resumen de consumos por departamento';
            $nombre_archivo="RESCONSDEPTO";
            $ruta = "almacen.reportes.resumen_consumos_depto";
            $headers=['CTA.','SCTA.','SSCTA.','CONCEPTO','CONSUMOS'];
            $papel = 'letter';
            $orientacion='portrait';
            /**
             * CONSULTAS A LA BD
             * 
             * La siguiente consulta en SQL es el equivalente a las consultas hechas con DB:
             * SELECT clave, detalles.descripcion, consumos.folio, detalles.cantidad, detalles.precio_unitario, detalles.subtotal, cat_oficinas.descripcion 
             * FROM consumos 
             * INNER JOIN detalles 
             * INNER JOIN cat_oficinas 
             * INNER JOIN cat_articulos 
             * WHERE consumos.id_oficina = cat_oficinas.id 
             * AND consumos.id_consumo = detalles.id_consumo 
             * AND detalles.id_articulo = cat_articulos.id 
             * GROUP BY clave, detalles.descripcion, consumos.folio, detalles.cantidad, detalles.precio_unitario, detalles.subtotal, cat_oficinas.descripcion
             */
            //dd($deptos);

            //Creando PDF con DOMPDF
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo',  'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
            
        }
        /**
         * REPORTE AUXILIAR DE ALMACÉN GENERAL
         */
        elseif ($auxAlmacen == "checked"){
            $mensaje = 'Reporte auxiliar de almacén general';
            $nombre_archivo="REPAUXALM";
            $ruta = "almacen.reportes.reporte_auxiliar";
            $headers=['CODIF.','DESCRIPCION', 'DEPARTAMENTO', 'MOVTO', 'NO. VALE','UNIDAD','CANT.','COSTO UNIT.','IMPORTE', 'INV. FIN'];
            $papel = 'letter';
            $orientacion='landscape';

            /**
             * CONSULTAS A LA BD
             * 
             * @partidas: Partidas de artículos que existen en el IPE
             * @articulos: cantidad de articulos en existencia que hay en el IPE 
             * @dpto_movto: movientos que se han hecho por departamentos por cada uno de los arituculos
             */
            

            $partidas = DB::table('cat_cuentas_contables')->select('id','sscta','nombre')
                ->orderBy('cat_cuentas_contables.sscta', 'asc')
                ->get();  
            $articulos = DB::table('cat_articulos')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->select('cat_articulos.id', 'cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'cat_articulos.existencias', 'cat_articulos.precio_unitario','cat_articulos.id_cuenta')
                ->where('existencias','>',0)
                ->orderBy('cat_articulos.clave', 'asc')
                ->get();
            $dpto_movto = DB::table('c_pedido_consumo')
                ->join('cat_oficinas', 'c_pedido_consumo.id_oficina', '=', 'cat_oficinas.id')
                ->join('d_pedido_consumo', 'c_pedido_consumo.id_pedido_consumo', '=', 'd_pedido_consumo.id_pedido_consumo_d')
                ->join('periodos', 'c_pedido_consumo.id_periodo', '=', 'periodos.id_periodo')
                ->join('cat_articulos', 'd_pedido_consumo.id_articulo', '=', 'cat_articulos.id')
                ->where('periodos.no_mes', '=', [$numMesInicio])
                ->select('c_pedido_consumo.tipo_movimiento','c_pedido_consumo.folio', 'd_pedido_consumo.cantidad', 'd_pedido_consumo.id_articulo', 'cat_oficinas.descripcion', 'cat_articulos.precio_unitario')
                ->get();
            //dd($dpto_movto);
            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }

            //Usando dompdf 
            $pdf = new Dompdf();
            $html = view($ruta,compact('orientacion','mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'partidas', 'articulos', 'dpto_movto'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
            
        }
        /**
         * REPORTE DE COMPRAS DE ALMACÉN GENERAL
         */
        elseif ($compAlmacen == "checked"){
            $mensaje = 'Reporte de compras de almacén general';
            $nombre_archivo="REPCOMPALM";
            $ruta = "almacen.reportes.reporte_compras";
            $headers=['CODIF.','DESCRIPCION','FACTURA','UNIDAD','CANT.','COSTO UNIT.','IMPORTE'];
            $papel = 'letter';
            $orientacion='portrait';
            /*$partidas = DB::table('cat_cuentas_contables')
                      ->select('cat_cuentas_contables.id', 'cat_cuentas_contables.sscta','cat_cuentas_contables.nombre')
                      ->get();*/
            $partidas = DB::table('cat_cuentas_contables')->select('id', 'sscta', 'nombre')->get();  

            $articulos = DB::table('detalles')
                      ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                      ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                      ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                      ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                      ->select('cat_articulos.clave', 'cat_articulos.descripcion', 'compras.no_factura', 'cat_unidades_almacen.descripcion_corta', 'detalles.cantidad', 'detalles.precio_unitario', 'detalles.subtotal', 'cat_articulos.id_cuenta')
                     /* ->where('periodos.estatus', '=', 1)*/
                     ->where('periodos.no_mes', '=', [$numMesInicio])
                      ->get();
            //dd($articulos); 


            $total_movimientos = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                    ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->groupBy('cat_cuentas_contables.sscta')
                    ->selectRaw(' cat_cuentas_contables.sscta as sscta, count(detalles.tipo_movimiento) as count ')
                    ->get();

            $total_cantidades = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                    ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->groupBy('cat_cuentas_contables.sscta')
                    ->selectRaw('cat_cuentas_contables.sscta as sscta, sum(detalles.cantidad) as sum_cantidad ')
                    ->get();
                    
            $total_subtotales = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                    ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->groupBy('cat_cuentas_contables.sscta')
                    ->selectRaw('cat_cuentas_contables.sscta as sscta, sum(detalles.subtotal) as sum_subtotal ')
                    ->get();  

            $total_movimientos_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('count(detalles.tipo_movimiento) as count ')
                    ->get();  
                    
            $total_cantidades_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('sum(detalles.cantidad) as sum_cantidad ')
                    ->get();  

            $total_subtotales_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('sum(detalles.subtotal) as sum_subtotal')
                    ->get();                 
                   
           //dd($total_cantidades_general);    

            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }

            //Usando dompdf
            //$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($ruta,compact('orientacion','mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'partidas', 'articulos', 'total_movimientos', 'total_cantidades', 'total_subtotales', 'total_movimientos_general', 'total_cantidades_general', 'total_subtotales_general'))->setPaper($papel, $orientacion);

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'partidas', 'articulos', 'total_movimientos', 'total_cantidades', 'total_subtotales', 'total_movimientos_general', 'total_cantidades_general', 'total_subtotales_general', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");

        }
        /**
         * REPORTE DE SIMULACIÓN DE ACTUALIZACIÓN DE COMPRAS
         */
        elseif ($simCompras == "checked"){
            $mensaje = 'Simulación de actualización de compras';
            $nombre_archivo="REPSIMCOM";
            $ruta = "almacen.reportes.reporte_simulacion_compras";
            $headers=['CODIF.','DESCRIPCION','UNIDAD','CANT. INICIAL', 'COSTO UNIT.','CANT. ADQ.', 'COSTO UNIT.','CANT. TOTAL', 'COSTO PROMEDIO',];
            $papel = 'letter';
            $orientacion='portrait';

            $articulos = DB::table('cat_articulos')
                ->join('detalles', 'cat_articulos.id', '=', 'detalles.id_articulo')
                ->join('inventario_inicial_final', 'detalles.id_articulo', '=', 'inventario_inicial_final.id_articulo')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                ->select('cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'inventario_inicial_final.cant_inicial', 'inventario_inicial_final.precio_inicial', 'detalles.cantidad', 'detalles.precio_unitario', 'detalles.subtotal')
               /* ->where('periodos.estatus', '=', 1)*/
                ->where('periodos.no_mes', '=', [$numMesInicio])
                ->orderBy('compras.no_factura',  'desc')
                ->get(); 
                
            dd($articulos);    
            
            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }
            
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");


        }
        /**
         * REPORTE DE VALIDACIÓN DE COMPRAS
         */
        elseif ($validCompras == "checked"){
            $mensaje = 'Reporte para validación de compras';
            $nombre_archivo="REPVALCOM";
            $ruta = "almacen.reportes.reporte_validacion_compras";
            $headers=['FACTURA','CODIF.','DESCRIPCION','UNIDAD','CANT.','COST. UNIT.','IMPORTE'];
            $papel = 'letter';
            $orientacion='portrait';

            $articulos = DB::table('detalles')
                ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                ->select('compras.no_factura', 'cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'detalles.cantidad', 'detalles.precio_unitario', 'detalles.subtotal')
               /* ->where('periodos.estatus', '=', 1)*/
                ->where('periodos.no_mes', '=', [$numMesInicio])
                ->orderBy('compras.no_factura',  'desc')
                ->get();

            //dd($articulos);

            $total_movimientos_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('count(detalles.tipo_movimiento) as count ')
                    ->get();  
                    
            $total_cantidades_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('sum(detalles.cantidad) as sum_cantidad ')
                    ->get();  

            $total_subtotales_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$numMesInicio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('sum(detalles.subtotal) as sum_subtotal')
                    ->get(); 

            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }     
            
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'articulos', 'total_movimientos_general', 'total_cantidades_general', 'total_subtotales_general', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");


        }
        /**
         * REPORTE DE VALIDACIÓN DE TRASLADOS
         */
        elseif ($validTraslados == "checked"){
            $mensaje = 'Reporte para validación de traslados';
            $nombre_archivo="REPVALTRAS";
            $ruta = "almacen.reportes.reporte_validacion_traslados";
            $headers=['FOLIO','CODIF. SAL.','DESCRIPCION','UNIDAD SAL.','CANT. ENTR.','COSTO SALIDA','IMPORTE SALIDA'];
            $papel = 'letter';
            $orientacion='portrait';
            
            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");


        }
        /**
         * REPORTE FINAL DE EXISTENCIAS
         */
        elseif ($existencias == "checked"){
            $mensaje = 'Reporte final de existencias';
            $nombre_archivo="REPFINALEXIST";
            $ruta = "almacen.reportes.reporte_final_existencias";
            $headers = ['CODIF.', 'DESCRIPCIÓN', 'UNIDAD', 'CANT.', 'COSTO', 'IMPORTE'];
            $papel = 'letter';
            $orientacion='landscape';

             /**
             * CONSULTAS A LA BD
             * 
             * @partidas: Partidas de artículos que existen en el IPE
             * @articulos: cantidad de articulos en existencia que hay en el IPE 
             * 
             */

            $partidas = DB::table('cat_cuentas_contables')->select('id', 'sscta', 'nombre')
                        ->orderBy('cat_cuentas_contables.sscta', 'asc')
                        ->get();  
            $articulos = DB::table('cat_articulos')
                    ->orderBy('cat_articulos.clave', 'asc')
                    ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                    ->select('cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'cat_articulos.existencias', 'cat_articulos.precio_unitario','cat_articulos.id_cuenta')
                    ->where('existencias','>',0)
                    ->get();
            
            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }
            
            $pdf = new Dompdf();
            $html = view($ruta,compact('orientacion','mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'partidas', 'articulos'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
        }
        /**
         * CONCENTRADO DE CONSUMOS POR ARTÍCULO
         */
        elseif ($consArticulo == "checked"){
            $mensaje = 'Concentrado de consumos por artículo';
            $nombre_archivo="CONCENTCONSARTI";
            $ruta = "almacen.concentrados.cons_p_articulo";
            $headers = ['CODIF.', 'DESCRIPCIÓN', 'UNIDAD', 'ENE. ', 'FEB. ', 'MAR. ', 'ABR. ', 'MAY. ', 'JUN. ', 'JUL. ', 'AGO. ', 'SEPT.', 'OCT.', 'NOV.','DIC.', 'TOT. DEL AÑO'];
            $papel = 'legal';
            $orientacion='landscape';

            /**
             * CONSULTAS A LA BD
             * 
             * @articulos: articulos consumidos durante los periodos establecidos
             * @total_art: articulos consumidos durante los periodo establecidos con informacion extra 
             * @t_tipos_art: total de articulos que fueron consumidos
             * 
             */

            $total_art = DB::table('cat_articulos')
                    ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                    ->join('d_pedido_consumo', 'cat_articulos.id' ,'=', 'd_pedido_consumo.id_articulo')
                    ->join('c_pedido_consumo', 'd_pedido_consumo.id_pedido_consumo', '=', 'c_pedido_consumo.id_pedido_consumo')
                    ->join('periodos', 'c_pedido_consumo.id_periodo', '=', 'periodos.id_periodo')
                    ->orderBy('cat_articulos.clave', 'asc')
                    ->select('cat_articulos.id','cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'd_pedido_consumo.cantidad', 'periodos.anio' )
                    ->where('d_pedido_consumo.cantidad', '>', 0)
                    ->where('periodos.anio', '=', [$yearInicio])
                    ->groupby('cat_articulos.clave')
                    ->get();
            $articulos = DB::table('periodos')
                    ->where('periodos.anio', '=', [$yearInicio])
                    ->whereBetween('no_mes', [$numMesInicio, $mesFin])
                    ->join('c_pedido_consumo', 'periodos.id_periodo', '=', 'c_pedido_consumo.id_periodo')
                    ->join('d_pedido_consumo', 'c_pedido_consumo.id_pedido_consumo', '=', 'd_pedido_consumo.id_pedido_consumo')
                    ->join('cat_articulos', 'd_pedido_consumo.id_articulo', '=', 'cat_articulos.id')
                    ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                    ->select('d_pedido_consumo.cantidad', 'c_pedido_consumo.id_periodo', 'cat_articulos.id', 'cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'periodos.no_mes' )
                    ->get();
            $t_tipos_art = DB::table('cat_articulos')
                    ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                    ->join('d_pedido_consumo', 'cat_articulos.id' ,'=', 'd_pedido_consumo.id_articulo')
                    ->join('c_pedido_consumo', 'd_pedido_consumo.id_pedido_consumo', '=', 'c_pedido_consumo.id_pedido_consumo')
                    ->join('periodos', 'c_pedido_consumo.id_periodo', '=', 'periodos.id_periodo')
                    ->orderBy('cat_articulos.clave', 'asc')
                    ->select('cat_articulos.id','cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'd_pedido_consumo.cantidad', 'periodos.anio' )
                    ->where('d_pedido_consumo.cantidad', '>', 0)
                    ->where('periodos.anio', '=', [$yearInicio])
                    ->groupby('cat_articulos.clave')
                    ->get()->count();
            
            
            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$yearInicio} al mes de {$mesF} de {$yearFin}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$yearInicio}";
            }

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'articulos','total_art', 'orientacion','numMesInicio','mesFin', 't_tipos_art' ));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");
        }
        /**
         * CONCENTRADO DE COMPRAS POR ARTÍCULO
         */
        elseif ($compArticulo == "checked"){
            $mensaje = 'Concentrado de compras por artículo';
            $nombre_archivo="CONCENTCOMPART";
            $ruta = "almacen.concentrados.compras_p_articulo";
            if($mesIni > 6) {
                $headers = ['CODIF.', 'DESCRIPCIÓN','JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE', 'TOTAL SEMESTRAL'];
            }else{
                $headers = ['CODIF.', 'DESCRIPCIÓN', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO','JUNIO', 'TOTAL SEMESTRAL'];
            }
            $papel = 'legal';
            $orientacion='landscape';
        }
        /**
         * CONCENTRADO DE EXISTENCIAS POR ARTÍCULO
         */
        elseif ($existArticulo == "checked"){
            $mensaje = 'Concentrado de existencias por artículo';
            $nombre_archivo="CONCENTEXISTART";
            $ruta = "almacen.concentrados.existencias_p_articulo";
            $headers = ['CODIF.', 'DESCRIPCIÓN', 'UNIDAD', 'ENE. ', 'FEB. ', 'MAR. ', 'ABR. ', 'MAY. ', 'JUN. ', 'JUL. ', 'AGO. ', 'SEPT.', 'OCT.', 'NOV.','DIC.', 'TOT. DEL AÑO'];
            $papel = 'legal';
            $orientacion='landscape';

            $partidas = DB::table('cat_cuentas_contables')->select('id', 'sscta', 'nombre')
                ->orderBy('cat_cuentas_contables.sscta', 'asc')
                ->get();  
            
            $articulos = DB::table('inventario_inicial_final')
                ->join('periodos', 'inventario_inicial_final.id_periodo', '=', 'periodos.id_periodo')
                ->join('cat_articulos', 'inventario_inicial_final.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_unidades_almacen', 'cat_articulos.id_unidad', '=', 'cat_unidades_almacen.id')
                ->whereBetween('no_mes', [$numMesInicio, $mesFin])
                ->where('periodos.anio', '=', [$yearInicio])
                ->select('periodos.no_mes','cat_articulos.id', 'cat_articulos.clave', 'cat_articulos.descripcion', 'cat_unidades_almacen.descripcion_corta', 'inventario_inicial_final.existencias')
                ->orderBy('cat_articulos.clave', 'asc')
                ->get();

            $periodos = DB::table('periodos')
                ->whereBetween('no_mes', [$numMesInicio, $mesFin])
                ->where('periodos.anio', '=', [$yearInicio])
                ->select('no_mes', 'anio')
                ->get();


            dd($periodos);

        }
        /**
         * CONCENTRADO DE CONSUMOS POR ÁREA Y ARTÍCULO
         */
        elseif ($consAreaArt == "checked"){
            $mensaje = 'Concentrado de consumos por área y artículo';
            $nombre_archivo="CONCENTCONSAART";
            $ruta = "almacen.concentrados.consumos_p_area";
            $headers = ['CODIF.', 'DESCRIPCIÓN', 'UNIDAD', 'ENE. ', 'FEB. ', 'MAR. ', 'ABR. ', 'MAY. ', 'JUN. ', 'JUL. ', 'AGO. ', 'SEPT.', 'OCT.', 'NOV.','DIC.', 'TOT. DEL AÑO'];
            $papel = 'legal';
            $orientacion='landscape';

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo',  'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");

        }/**
         * CONCENTRADO DE CONSUMOS DE ARTÍCULO POR AREA
         */
        elseif ($consArtArea == "checked"){
            $mensaje = 'Concentrado de consumos de artículo por área';
            $nombre_archivo="CONCENTCONSARTA";
            $ruta = "almacen.concentrados.consumos_p_art_area";
            $headers = ['CODIF.', 'DESCRIPCIÓN', 'UNIDAD', 'ENE. ', 'FEB. ', 'MAR. ', 'ABR. ', 'MAY. ', 'JUN. ', 'JUL. ', 'AGO. ', 'SEPT.', 'OCT.', 'NOV.','DIC.', 'TOT. DEL AÑO'];
            $papel = 'legal';
            $orientacion='landscape';

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo',  'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");

        }/**
         * CONCENTRADO DE GASTO POR DEPARTAMENTO
         */
        elseif ($gastoDepto == "checked"){
            $mensaje = 'Concentrado de gastos a la fecha por departamento';
            $nombre_archivo="CONCENTGASTDEPTO";
            $ruta = "almacen.concentrados.gasto_depto";
            $headers = ['UBPP', 'DEPARTAMENTO', 'ESC. Y OFNA.', 'FORM. IMPR. ', 'MAT. COMP. ', 'MAT. IMPR. ', 'MAT. LIMP. ', 'MAT. FERRET. ', 'M. FOT. CIN.', 'IMPORTE TOTAL'];
            $papel = 'legal';
            $orientacion='landscape';

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo',  'pdf', 'orientacion'));
            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");

        }else{
           return back()->with('warning',"Porfavor seleccione un tipo de reporte");
        }

       

        return $pdf->stream($nombre_archivo);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function nombre_mes($no_mes){
        $mes="";
        switch ($no_mes) {
            case 1:
                $mes = "enero";
                break;
            case 2:
                $mes = "febrero";
                break;
            case 3:
                $mes = "marzo";
                break;
            case 4:
                $mes = "abril";
                break;
            case 5:
                $mes = "mayo";
                break;
            case 6:
                $mes = "junio";
                break;
            case 7:
                $mes = "julio";
                break;
            case 8:
                $mes = "agosto";
                break;
            case 9:
                $mes = "septiembre";
                break;
            case 10:
                $mes = "octubre";
                break;
            case 11:
                $mes = "noviembre";
                break;
            case 12:
                $mes = "diciembre";
                break;
            default:
                $mes = "";
                break;
        }
        return $mes;
    }
}