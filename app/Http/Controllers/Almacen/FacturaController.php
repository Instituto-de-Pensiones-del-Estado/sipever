<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Doctrine\DBAL\Driver\PDOConnection;
use Exception;
use DB;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partidas = DB::select("call sp_get_grupos");
        $proveedores = DB::select("call sp_get_proveedores");
        return view('almacen.facturas.facturas', compact('partidas','proveedores'));
    }

    public function getArticulos(Request $request){
        $nombrePartida = $request->input('partida');
        $articulos = DB::select("call sp_obtener_articulos_grupo(?)", array($nombrePartida));
        return json_encode($articulos);
    }

    /**
     * Método para registrar una factura por la compra de uno o varios artículos registrados en almacén
     * @param Request $request
     * @return Response
    */

    public function registrarFactura(Request $request){
        // Obtenemos los datos de todos los artículos ingresados
        $descripciones = $request->descripcionArticulo;
        $precios = $request->precioArticulo;
        $cantidades = $request->cantidadArticulo;
        $articulos = $request->claveArticulo;
        if(!$articulos){
            return back()->with('warning','Porfavor ingrese al menos un artículo');
        }
        $subtotal = $request->subtotal;
        $total_aux = 0;
        for ($i=0; $i < sizeof($articulos); $i++) {
            $subtotal_aux = $precios[$i] * $cantidades[$i];
            $total_aux = $total_aux + $subtotal_aux;
        }

        if($subtotal != $total_aux){
            return back()->with('warning','Advertencia, los precios no coinciden con el total, verifique los datos de la factura');
        }
       /* if($total_aux > 101000){
            return back()->with('warning','Advertencia, Está registando un precio que excede el límite permitido, favor de contactar el Departamento de tecnologías de la información');
        }*/

        if(empty($articulos)){
            return back()->with('warning','Porfavor ingrese al menos un articulo');
        }else{
            date_default_timezone_set('America/Mexico_City');
            $dia = date("d");
            $no_mes=date("n");
            $anio=date("Y");
            $nombreProveedor = $request->proveedor;
            $fecha_movimiento = $request->fecha_ingreso;
            $no_factura = $request->noFactura;
            $fecha_facturacion = $request->fecha_facturacion;
            $parts_fact = explode('-',$fecha_facturacion);
            $parts_mov=explode('-',$fecha_movimiento);

            if ($parts_fact[0] > $parts_mov[0]) {
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }elseif ($parts_fact[1]>$parts_mov[1]) {
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }elseif($parts_fact[2]>$parts_mov[2]){
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }

            if ($parts_fact[0] > $anio || $parts_mov[0] > $anio) {
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }elseif ($parts_fact[1] > $no_mes || $parts_mov[1] > $no_mes) {
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }elseif($parts_fact[2] > $dia || $parts_mov[2] > $dia){
                return back()->with('warning', "Las fechas ingresadas no son válidas, porfavor asegurese de ingresar la información correcta");
            }

            $iva=$request->iva;
            $subtotal = $request->subtotal;
            //Obtenemos los datos de la conexión con la base de datos
            $db = DB::connection()->getPdo();
            //Establecemos la conexión
            $db->setAttribute(PDOConnection::ATTR_ERRMODE, PDOConnection::ERRMODE_EXCEPTION);
            $db->setAttribute(PDOConnection::ATTR_EMULATE_PREPARES, true);

            //Preparamos la llamada al procedimiento remoto
            $query = $db->prepare('CALL sp_factura(?,?,?,?,?,?,?,?,@clave)');
            //Hacemos un binding de los parámetros, así protegemos nuestra
            //llamada de una posible inyección sql
            $query->bindParam(1,$no_mes);
            $query->bindParam(2,$anio);
            $query->bindParam(3,$nombreProveedor);
            $query->bindParam(4,$fecha_movimiento);
            $query->bindParam(5,$no_factura);
            $query->bindParam(6,$fecha_facturacion);
            $query->bindParam(7,$iva);
            $query->bindParam(8,$subtotal);
            try {
                //Ejecutamos el procedimiento
                $query->execute();
                $query->closeCursor();
                //accedemos al valor de retorno para regresar la vista correspondiente.
                $results = $db->query('SELECT @clave AS result')->fetch(PDOConnection::FETCH_ASSOC);

                //Si el valor de retorno no está vacío y es diferente de null
                if ($results) {
                    // Obtenemos la clave generada
                    $clave_generada = $results['result'];

                    // Se manda a llamar al procedimiento almacenado para registrar los detalles de la factura
                    // Se llama al procedimiento una vez por cada artículo que corresponda a la factura
                    for ($i=0; $i < sizeof($articulos) ; $i++) {
                        $query = $db->prepare('CALL sp_detalles_compra(?,?,?,?)');
                        $query->bindParam(1,$clave_generada);
                        $query->bindParam(2,$descripciones[$i]);
                        $query->bindParam(3,$cantidades[$i]);
                        $query->bindParam(4,$precios[$i]);

                        $query->execute();
                        $query->closeCursor();
                    }
                    return redirect()->route('almacen.facturas.index')->with('success','Factura registrada correctamente');
                }elseif (empty($result)) {
                    // Si el resultado viene vacío se regresa una advertencia
                    return back()->with('warning', "Error al registrar datos de factura, intente de nuevo mas tarde \nSi el problema persiste contacte al departamento de tecnologías de la información");
                }else{
                    return back()->withErrors(['msg', "Error de base de datos \n Contacte al departamento de tecnologías de la información"]);
                }
            } catch (Exception $e) {
                $mensaje = "{$e->getMessage()} \n Contacte al departamento de tecnologías de la información";
                return back()->withErrors([$mensaje]);
            }
        }
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
}
