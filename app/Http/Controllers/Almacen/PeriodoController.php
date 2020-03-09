<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Driver\PDOConnection;
use App\Http\Controllers\Controller;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = DB::select('SELECT DISTINCT anio FROM periodos');
        return view('almacen.cierre_mes', compact('years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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

    /**
     * Cierra el mes ingresado y crea uno nuevo.
     * @param int $no_mes
     * @param string $anio
     * @return \Illuminate\Http\Response
     */
    public function cerrar_mes(Request $request){
        $no_mes = $request->input('numMes');
        $anio = $request->input('year');

        $periodos = DB::select('SELECT * FROM periodos');
        $meses = [];
        $years = [];

        foreach ($periodos as $periodo) {
            foreach ($periodo as $key => $value) {
                if($key == 'no_mes'){
                    array_push($meses,$value);
                }elseif ($key == 'anio') {
                    array_push($years,$value);
                }
            }
        }
        
        if(!in_array($no_mes, $meses) || !in_array($anio, $years)){
            return redirect()->route('almacen.periodo.index')->with('warning', "El mes ingresado no existe o está cerrado, intente mas tarde\nSi el problema persiste contacte al departamento de tecnologías de la información");
        }
        $estatus = $this->getStatus($no_mes, $anio, $periodos);
        if($estatus != 1){
            return redirect()->route('almacen.periodo.index')->with('warning', "El mes ingresado no existe o está cerrado, intente mas tarde\nSi el problema persiste contacte al departamento de tecnologías de la información");
        }
        $result = DB::select('CALL sp_cerrar_periodo(?,?)', array($no_mes, $anio))[0]->result;
        if ($result==3) {
            return redirect()->route('almacen.polizas.index')->with('success', 'Mes cerrado exitosamente, favor de generar la poliza correspondiente');
        }elseif ($result ==2) {
            return redirect()->route('almacen.periodo.index')->with('warning', "Error al generar nuevo mes, asgurese de que sea el mes y año correctos \nSi el problema persiste contacte al departamento de tegnologías de la información");
        }else{
            return redirect()->route('almacen.periodo.index')->withErrors(['msg', "Error de base de datos \n Contacte al departamento de tecnologías de la información"]);
        }
    }

    private function getStatus($no_mes, $anio, $periodos){
        foreach ($periodos as $periodo) {
            if($periodo->no_mes == $no_mes && $periodo->anio==$anio){
                return $periodo->estatus;
            }
        }
    }

}
