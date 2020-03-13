<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Pagination\LengthAwarePaginator;
use DB;
use Illuminate\Support\Facades\Input;

class PartidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $no_index = 0;
        $partidas = DB::select("call sp_get_grupos");

        return view('almacen.partidas.partidas',compact('partidas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $cta = $request->cta;
        $scta = $request->scta;
        $sscta = $request->sscta;
        $nombre_aux = $request->nombre;
        $nombre = strtoupper($nombre_aux);
        $grupo=$request->grupo;
        $ctaarmo = $request->ctaarmo;
        $nomarmo_aux = $request->nomarmo;
        $nomarmo = strtoupper($nomarmo_aux);

        /*if (!is_numeric($cta) || !is_numeric($scta) || !is_numeric($sscta) || !is_numeric($ctaarmo)){
             return back()->with('warning','Los datos ingresdos no son correctos');
        }*/

        if(strlen($grupo)>1){
            return back()->with('warning','Los datos ingresdos no son correctos');
        }

        try {
            DB::select("call sp_almacenar_grupo(?,?,?,?,?,?,?,?)", array($cta, $scta, $sscta, $nombre, $ctaarmo, $nomarmo, $grupo, 1));
            return redirect()->route('almacen.partidas.index')
                        ->with('success','Partida guardada exitosamente');
        } catch (Exception $e) {
            return back()->withErrors(['msg','Error en alguna parte']);
        }


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
    public function update($id)
    {
        //dd($id);
        $input = Input::only('cta','scta','sscta', 'nombre', 'grupo','ctaarmo', 'nomarmo');
        $cta = $input['cta'];
        $scta = $input['scta'];
        $sscta = $input['sscta'];
        
        $ctaarmo = $input['ctaarmo'];
        //dd($ctaarmo);
        if (!is_numeric($cta) || !is_numeric($scta) || !is_numeric($sscta) || !is_numeric(str_replace('.','',$ctaarmo))){
             return back()->with('warning','Los datos ingresdos no son correctos');
        }
        $nombre_aux = $input['nombre'];
        $nombre =strtoupper($nombre_aux);
        $grupo=$input['grupo'];
        if(strlen($grupo)>1){
            return back()->with('warning','Los datos ingresdos no son correctos');
        }
        $nomarmo_aux = $input['nomarmo'];
        $nomarmo = strtoupper($nomarmo_aux);

        try {
            DB::select("call sp_actualizar_grupo(?,?,?,?,?,?,?,?,?)", array($id, $cta, $scta, $sscta, $nombre, $ctaarmo, $nomarmo, $grupo, 1));
            return redirect()->route('almacen.partidas.index')
                        ->with('success','Partida actualizada exitosamente');
        } catch (Exception $e) {
            return back()->withErrors(['msg','Error en alguna parte']);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
         $nombre = $request->nombre;
         $id= $request->id;
         if(empty($nombre)){
            return back()->with('warning','Porfavor seleccione una partida para reasignar los artículos');
         } else{
            try {
                DB::select("call sp_eliminar_grupo(?,?)", array($id,$nombre));
                return redirect()->route('almacen.partidas.index')
                            ->with('success','Partida eliminada exitosamente');
            } catch (Exception $e) {
                return back()->withErrors(['msg','Error en alguna parte']);
            }
         }
    }

    public function eliminar_tildes($cadena_aux){

        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        $cadena = utf8_encode($cadena_aux);

        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );

        return strtoupper($cadena);
    }
}
