@extends('almacen.reportes.encabezado_reporte')
@section('content')
<!-- CREACIÓN DE HEADERS DE ACUERDO AL REPORTE -->
<table>   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <tbody>
<!-- INICIALIZANDO ACUMULADORES DE VALORES POR PARTIDAS -->
@php
    $t_consumos_partida=0;
    $t_arts_partida=0;
    $t_importe_partida=0;

    $t_consumos_depto=0;
    $t_arts_depto=0;
    $t_importe_depto=0;
@endphp

    <!-- FOREACH DEPARTAMENTOS -->
    @foreach ($deptos as $departamentos)
            <!-- CREACIÓN DE SEPARADOR POR DEPARTAMENTO -->
            <tr>
                <th colspan="1">ÁREA:</th>
                <th colspan="1">{{$departamentos->ubpp}}</th>
                <th colspan= "5" align="left" style="padding-right:5px">{{$departamentos->descripcion}}</th>
            </tr>
            
            <!-- FOREACH PARTIDAS -->
            
            @foreach ($partidas as $seccion_partidas)
                <!-- FOREACH CONDICIONAL -->
                <!-- Las condiciones dentro de este foreach permiten que sólo se impriman las partidas que tengan consumos por parte -->
                <!-- del departamento en el cual nos encontremos. -->
                @foreach ($consumos as $check_consumo)
                    @if( $seccion_partidas->id == $check_consumo->id_cuenta and $departamentos->ubpp == $check_consumo->ubpp)
                    <tr>
                        <th colspan="2" style="border-bottom: 2px solid #dee2e6">PARTIDA: </th>
                        <th colspan="5"style="border-bottom: 2px solid #dee2e6; text-align: left;"> 
                            {{$seccion_partidas->sscta}} {{$seccion_partidas->nombre}}
                        </th>
                    </tr>
                    @break
                    @endif
                @endforeach
                
                <!-- FOREACH CONSUMOS-->
                <!-- Por cada consumo que se haya recuperado se imprimen los detalles si coincide con la partida y el depto. en cuestión -->
                @foreach ($consumos as $validConsumos)
                    @if($seccion_partidas->id == $validConsumos->id_cuenta and $validConsumos->ubpp == $departamentos->ubpp)
                    <tr>
                        <td scope= "row">{{$validConsumos->folio}}</td>
                        <td>{{$validConsumos->clave}}</td>
                        <td>{{$validConsumos->descripcion}}</td>
                        <td>{{$validConsumos->descripcion_corta}}</td>
                        <td>{{$validConsumos->cantidad}}</td>
                        <td>{{$validConsumos->precio_unitario}}</td>
                        <td>{{$validConsumos->subtotal}}</td>
                    </tr>
                    @php
                        
                        $t_consumos_partida += 1;
                        $t_arts_partida += $validConsumos->cantidad;
                        $t_importe_partida += $validConsumos->subtotal;

                        
                    @endphp
                    @endif
                @endforeach
                <!-- TERMINA FOREACH CONSUMOS-->
                
                <!-- FOREACH CONDICIONAL -->
                <!-- Las condiciones dentro de este foreach permiten que sólo se impriman los resultados de las partidas que tengan consumos por parte -->
                <!-- del departamento en el cual nos encontremos. -->
                @foreach ($consumos as $check_consumo)
                    @if( $seccion_partidas->id == $check_consumo->id_cuenta and $departamentos->ubpp == $check_consumo->ubpp)
                    <tr>
                        <th style="border-top: 2px solid #dee2e6">TOTALES POR PARTIDA</th>
                        <th style="border-top: 2px solid #dee2e6">CONSUMOS:</th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_consumos_partida}}</th>
                        <th style="border-top: 2px solid #dee2e6">ARTÍCULOS:</th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_arts_partida}}</th>
                        <th style="border-top: 2px solid #dee2e6"></th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_importe_partida}}</th>
                    </tr>
                    @break
                    @endif
                @endforeach

                <!-- -->
                @php
                    $t_consumos_partida=0;
                    $t_arts_partida=0;
                    $t_importe_partida=0;
                 @endphp
            @endforeach 
            <!-- TERMINA FOREACH PARTIDAS-->
            <tr>
                <td></td>
                <td>ESTO DEBERÍA IR DESPUÉS DE CADA DEPARTAMENTO</td>
                <td></td>
            </tr>
    @endforeach
    <!-- TERMINA FOREACH DEPARTAMENTOS -->
    </tbody>
</table>
<!--    
<table>
    <thead>
        <tr>
            <th>TOTAL DE ARTÍCULOS POR PARTIDAS</th>
            <th>CANTIDAD DE ARTÍCULOS</th>
            <th>IMPORTES TOTALES</th>
        </tr>
    </thead>
    <tbody>
        @foreach($partidas as $resumen_partidas)
            <tr>
                <td>{{$resumen_partidas->sscta}} {{$resumen_partidas->nombre}}</td>
                <td>20</td>
                <td>20</td>
            </tr>
        @endforeach
    </tbody>
    <th colspan="2">{{$total_consumos}} CONSUMOS</th>
    <th colspan="2">ARTÍCULOS: {{$total_articulos}}</th>
    <th colspan="4">IMPORTE TOTAL: {{$total_importe}}</th>-->



<footer>
</footer>
@endsection