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

    $t_consumos_general=0;
    $t_arts_general=0;
    $t_importe_general=0;
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
                    <td></td>
                    @break
                    @endif
                @endforeach
                <!-- TERMINA EL FOREACH CONDICIONAL -->

                <!-- Se usan variables contadoras para imprimir los totales por partida, depto. y en total. -->
                <!-- Se reinician los valores de algunas para evitar mala acumulación -->
                @php
                    $t_consumos_depto += $t_consumos_partida;
                    $t_arts_depto += $t_arts_partida;
                    $t_importe_depto += $t_importe_partida;

                    $t_consumos_partida=0;
                    $t_arts_partida=0;
                    $t_importe_partida=0;
                 @endphp

            @endforeach 
            <!-- TERMINA FOREACH PARTIDAS-->

            <tr>
                        <th style="border-top: 2px solid #dee2e6">TOTALES POR DEPARTAMENTO</th>
                        <th style="border-top: 2px solid #dee2e6">CONSUMOS:</th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_consumos_depto}}</th>
                        <th style="border-top: 2px solid #dee2e6">ARTÍCULOS:</th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_arts_depto}}</th>
                        <th style="border-top: 2px solid #dee2e6"></th>
                        <th style="border-top: 2px solid #dee2e6">{{$t_importe_depto}}
                        <div class="page-break"></div>
                        </th>
                        
            </tr>
            <td colspan="7" style="border-bottom: 2px solid #dee2e6"></td>
            

            <!-- Se usan variables contadoras para imprimir los totales por partida, depto. y en total. -->
            <!-- Se reinician los valores de algunas para evitar mala acumulación -->
            @php
                $t_consumos_general += $t_consumos_depto;
                $t_arts_general += $t_arts_depto;
                $t_importe_general += $t_importe_depto;

                $t_consumos_depto=0;
                $t_arts_depto=0;
                $t_importe_depto=0;
            @endphp
            
    @endforeach
    <!-- TERMINA FOREACH DEPARTAMENTOS -->
    </tbody>
</table style="border-bottom: 2px solid #dee2e6; text-align: left;">

<!-- TABLA RESUMEN POR PARTIDAS -->
<!-- La siguiente tabla es la suma de los consumos por partida y al final se muestran los totales de todos los consumos-->
<table border="0s">
    <thead>
        <tr>
            <th>TOTAL DE ARTÍCULOS POR PARTIDAS</th>
            <th>CANTIDAD DE ARTÍCULOS</th>
            <th>IMPORTES TOTALES</th>
        </tr>
    </thead>
    <tbody>
        <!-- FOREACH PARTIDAS -->
        <!-- Se usa para imprimir los consumos (artículos e importe) por partida --> 
        @foreach($partidas as $resumen_partidas)
            @foreach ($consumos as $contador_consumos)
                @if($resumen_partidas->id == $contador_consumos->id_cuenta)
                    @php
                        $t_arts_partida += $contador_consumos->cantidad;
                        $t_importe_partida += $contador_consumos->subtotal;
                    @endphp
                @endif
            @endforeach
            <tr>
                <td>{{$resumen_partidas->sscta}} {{$resumen_partidas->nombre}}</td>
                <td>{{$t_arts_partida}}</td>
                <td>{{$t_importe_partida}}</td>
            </tr>

            @php
                $t_arts_partida=0;
                $t_importe_partida=0;
            @endphp
        @endforeach
        <!-- TERMINA FOREACH PARTIDAS -->
    </tbody>
    <th colspan="2">{{$total_consumos}} CONSUMOS</th>
    <th colspan="2">ARTÍCULOS: {{$total_articulos}}</th>
    <th colspan="4">IMPORTE TOTAL: {{$total_importe}}</th>
<!-- TERMINA TABLA RESUMEN POR PARTIDAS -->



<footer>
</footer>
@endsection