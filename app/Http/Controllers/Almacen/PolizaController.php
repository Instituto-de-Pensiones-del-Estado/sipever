<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use DB;

use Dompdf\Dompdf;
use Dompdf\Options;


class PolizaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = DB::select('SELECT DISTINCT anio FROM periodos');
        return view('almacen.polizas', compact('years'));
    }

    /**
     * Método par ala generación de poliza
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generarPoliza(Request $request){
        $almacen = $request->input('almacen');
        $conta = $request->input('conta');
        $no_mes = $request->input('numMes');
        $anio = $request->input('year');
        //$numMesInicio = $request->input('numMes');
        $mesIni = $this->nombre_mes($no_mes);
        $periodo = $request->has('numMes') && $request->has('year') ? true : false;
        $archivo = file_get_contents(public_path("/img_system/low_res_logo.png"));
        $imagen_b64 = base64_encode($archivo);
        $logo_b64 = "data:image/png;base64,{$imagen_b64}";
        $fecha = date("d/M/Y");
        $hora = date("h:i a");
        $pdf = null;
        $tipo = 'reporte';

        $mes_nombre = $this->nombre_mes($no_mes);
        $ruta = "";
        $headers = [];
        $mensaje = "";
        $nombre_archivo="";

        if($almacen == "checked"){
            $mensaje = "Poliza de almacén";
            $nombre_archivo="POLIZALMAC";
            $ruta = "almacen.polizas.poliza_almacen";
            $headers = ['CTA.', 'SCTA', 'SSCTA', 'CONCEPTO.', 'CARGOS', 'ABONOS', "TOTALES\n(CONSUMOS)"];
            $headers2 = ['CTA.', 'SCTA', 'SSCTA', 'DEPARTAMENTOS'];
            $papel = 'letter';
            $orientacion='portrait';

            /********************************
             * Sección de compras 
             *******************************/
            //partidas identificación 
            $partidasCompras = DB::table('detalles')
            ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
            ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
            ->select('cat_cuentas_contables.cta','cat_cuentas_contables.scta','cat_cuentas_contables.sscta', 'cat_cuentas_contables.nombre')
            ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
            ->join('periodos', 'compras.id_periodo', '=', 'periodos.id_periodo')
            ->where('periodos.estatus', '=', 1)
            ->where('periodos.no_mes', '=', [$no_mes])
            ->where('detalles.tipo_movimiento', '=', 3)
            ->orderBy('cat_cuentas_contables.cta','ASC')
            ->orderBy('cat_cuentas_contables.sscta','ASC')
            ->groupBy('sscta')
            ->get();  
            //dd($partidasCompras);

            $total_subtotales = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$no_mes])
                    ->where('periodos.anio', '=', [$anio])
                    ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                    ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->groupBy('cat_cuentas_contables.sscta')
                    ->selectRaw('cat_cuentas_contables.cta as cta, cat_cuentas_contables.scta as scta, cat_cuentas_contables.sscta as sscta, sum(detalles.subtotal) as sum_subtotal ')
                    ->get();  

            //dd($total_subtotales);

            $total_subtotales_general = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 3)
                    ->where('periodos.no_mes', '=', [$no_mes])
                    ->where('periodos.anio', '=', [$anio])
                    ->join('compras', 'detalles.id_compra', '=', 'compras.id_compra')
                    ->join('periodos', 'compras.id_periodo', "=", 'periodos.id_periodo')
                    ->selectRaw('sum(detalles.subtotal) as sum_subtotal')
                    ->get();    

            //dd($total_subtotales_general);

            /********************************
             * Consumos por departamento
             *******************************/   
            //Obtener el nombre del las oficinas      
            $deptosConsumos = DB :: table('d_pedido_consumo')
                ->join('c_pedido_consumo', 'd_pedido_consumo.id_pedido_consumo', '=', 'c_pedido_consumo.id_pedido_consumo')
                ->join('consumos', 'c_pedido_consumo.id_pedido_consumo', '=', 'consumos.id_pedido_consumo')
                ->join('detalles', 'consumos.id_consumo', '=', 'detalles.id_consumo')
                ->join('cat_oficinas', 'consumos.id_oficina', '=', 'cat_oficinas.id')
                ->join('cat_articulos', 'd_pedido_consumo.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                ->join('periodos','consumos.id_periodo', '=', 'periodos.id_periodo')
                ->where('periodos.estatus', '=', 1)
                ->where('periodos.no_mes', '=', [$no_mes])
                ->select('cat_oficinas.ubpp', 'cat_cuentas_contables.cta', 'cat_cuentas_contables.scta', 'cat_cuentas_contables.sscta', 'cat_oficinas.descripcion')
                ->groupBy('cat_cuentas_contables.sscta')
                ->get();
            //dd($deptosConsumos);
            
            $total_subtotalesConsumos = DB::table('detalles')
                    ->where('detalles.tipo_movimiento', '=', 1)
                    ->where('periodos.no_mes', '=', [$no_mes])
                    ->where('periodos.anio', '=', [$anio])
                    ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                    ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                    ->join('consumos', 'detalles.id_consumo', '=', 'consumos.id_consumo')
                    ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                    ->groupBy('cat_cuentas_contables.sscta')
                    ->selectRaw('cat_cuentas_contables.cta as cta, cat_cuentas_contables.scta as scta, cat_cuentas_contables.sscta as sscta, sum(detalles.subtotal) as sum_subtotal ')
                    ->get();  
            //dd($total_subtotalesConsumos);

            $total_subtotalesConsumos_general = DB::table('detalles')
            ->join('consumos', 'detalles.id_consumo', '=', 'consumos.id_consumo')
            ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
            ->where('detalles.tipo_movimiento', '=', 1)
            ->where('periodos.no_mes', '=', [$no_mes])
            ->where('periodos.anio', '=', [$anio])
            ->where('periodos.estatus', '=', 1)
            ->selectRaw('sum(detalles.subtotal) as sum_subtotal')
            ->get();    

            //dd($total_subtotalesConsumos_general);

            /******************************* 
            *Consumos por partida
            ******************************/
            $partidasConsumos = DB::table('detalles')
                ->join('cat_articulos', 'detalles.id_articulo', '=', 'cat_articulos.id')
                ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                ->select('cat_cuentas_contables.cta','cat_cuentas_contables.scta','cat_cuentas_contables.sscta', 'cat_cuentas_contables.nombre')
                ->join('consumos', 'detalles.id_consumo', '=', 'consumos.id_consumo')
                ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                ->where('periodos.estatus', '=', 1)
                ->where('periodos.no_mes', '=', [$no_mes])
                ->where('detalles.tipo_movimiento', '=', 1)
                ->orderBy('cat_cuentas_contables.cta','ASC')
                ->orderBy('cat_cuentas_contables.sscta','ASC')
                ->groupBy('cat_cuentas_contables.nombre')
                ->get();  
            //dd($partidasConsumos);      
            
            $total_subtotalesPCons = DB::table('detalles')
                ->where('detalles.tipo_movimiento', '=', 1)
                ->where('periodos.estatus', '=', 1)
                ->where('periodos.no_mes', '=', [$no_mes])
                ->where('periodos.anio', '=', [$anio])
                ->join('cat_articulos', 'cat_articulos.id', '=', 'detalles.id_articulo')
                ->join('cat_cuentas_contables', 'cat_articulos.id_cuenta', '=', 'cat_cuentas_contables.id')
                ->join('consumos', 'detalles.id_consumo', '=', 'consumos.id_consumo')
                ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                ->groupBy('cat_cuentas_contables.sscta')
                ->selectRaw('cat_cuentas_contables.cta as cta, cat_cuentas_contables.scta as scta, cat_cuentas_contables.sscta as sscta, sum(detalles.subtotal) as sum_subtotal ')
                ->get();  

            $total_subtotalesPCons_general = DB::table('detalles')
                ->where('detalles.tipo_movimiento', '=', 1)
                ->where('periodos.estatus', '=', 1)
                ->where('periodos.no_mes', '=', [$no_mes])
                ->where('periodos.anio', '=', [$anio])
                ->join('consumos', 'detalles.id_consumo', '=', 'consumos.id_consumo')
                ->join('periodos', 'consumos.id_periodo', "=", 'periodos.id_periodo')
                ->selectRaw('sum(detalles.subtotal) as sum_subtotal')
                ->get();    

           
            if($periodo){
                $mensaje = "{$mensaje} del mes de {$mesIni} de {$anio} al mes de {$mesIni} de {$anio}";
            }else{
                $mensaje = "{$mensaje} correspondiente al mes de {$mesIni} de {$anio}";
            }

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'partidasCompras',  'total_subtotales', 'total_subtotales_general', 'pdf', 'orientacion',  'deptosConsumos', 'total_subtotalesConsumos', 'total_subtotalesConsumos_general', 'partidasConsumos', 'total_subtotalesPCons', 'total_subtotalesPCons_general',  'headers2'));

            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");





        }elseif($conta == "checked"){
            $mensaje="Poliza para contabilidad y presupuesto";
            $nombre_archivo="POLIZACONTPRESUP";
            $ruta = "almacen.polizas.contabilidad_presup";
            $headers = ['CTA.', 'SCTA', 'SSCTA', 'CONCEPTO.', 'CARGOS', 'ABONOS'];
            $papel = 'letter';
            $orientacion='portrait';


            /*** 
            * Consulta a la segunda base de datos
            ***/
            $polizas = DB::connection('mysql2')->table('polizas')
            ->where('TIPOPOL','=','A')
            ->where('AÑO', '=', 2019)
            ->where('NUMPOL', '=', 1376)
            ->select('CUENTAP', 'SUBCTAP', 'SSUBCTAP', 'CONCEPTO', 'IMPORTE')
            ->get();
            //dd($polizas);

            $total = DB::connection('mysql2')->table('polizas')
            ->where('TIPOPOL','=','A')
            ->where('AÑO', '=', 2019)
            ->where('NUMPOL', '=', 1376)
            ->where('CUENTAP', '=', 3103)
            ->select('IMPORTE')
            ->get();

            $pdf = new Dompdf();
            $html = view($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers', 'tipo', 'pdf', 'orientacion', 'polizas', 'total'));

            $pdf -> setPaper($papel, $orientacion);
            $options = new Options();
            $options -> set(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
            $pdf -> setOptions($options);
            $pdf -> loadHtml($html);
            $pdf -> render();
            return $pdf->stream($nombre_archivo.".pdf");

            

        }else{
            return back()->with('warning',"Porfavor seleccione un tipo de poliza");
        }

        $mensaje = "{$mensaje} correspondiente al mes de {$mes_nombre} de {$anio}";
        $tipo='poliza';
        date_default_timezone_set('America/Mexico_City');
        $fecha_nombre=date("Ymd");
        $hora_nombre=date("Hi");
        $nombre_archivo = "{$fecha_nombre}_{$nombre_archivo}_{$hora_nombre}";

        $archivo = file_get_contents(public_path("/img_system/banner_principal.png"));
        $imagen_b64 = base64_encode($archivo);
        $logo_b64 = "data:image/png;base64,{$imagen_b64}";

        $fecha = date("d/M/Y");
        $hora = date("h:i a");
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView($ruta,compact('mensaje','fecha','hora','logo_b64', 'headers','tipo'));

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
