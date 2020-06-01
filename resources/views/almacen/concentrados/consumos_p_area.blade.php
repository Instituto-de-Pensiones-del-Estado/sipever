@extends('almacen.reportes.encabezado_reporte')
@section('content')

<table border="0">   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
    </thead>

<tbody>
<!-- FOREACH Deptos comienza la división por departamentos (oficina == 0) -->
@foreach ($deptos as $depto)
   
    <tr>
        <th>UBPP{{$depto->ubpp}}</th>
        <th>DEPARTAMENTO</th>
        <th>{{$depto->descripcion}}</th>
        <th colspan="13"></th>
    </tr>
    <tr>
        <td colspan="16" style="border-bottom: 2px solid #dee2e6"></td>
    </tr>
    @php
        $articulos_p_depto=0;
        $total_suma_depto=0;
        $total_p_depto = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
    @endphp
    <span style="font-weight:bold"></span>
    <!-- FOREACH Oficinas comienza la división por oficinas (oficinas != 0) -->
    @foreach ($oficinas as $oficina)
        @if ($oficina->ubpp == $depto->ubpp)
            <tr> 
                <th>OFICINA</th>
                <th>{{$oficina->oficina}}</th>
                <th>{{$oficina->nombre_oficina}}</th>
                <th colspan="13"></th>
            </tr>
            <tr>
                <td colspan="16" style="border-bottom: 2px solid #dee2e6"></td>
            </tr>
            <!-- Se declaran los contadores y acumuladores para  -->
            @php
                $articulos_p_oficina=0;
                $acumulador_consumo=0;
                $total_p_articulo =0;
                $total_periodo = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
                $total_p_oficina =0;
            @endphp
            <!-- FOREACH ARTÍCULOS: repasa por cada uno de los artículos que hayan tenido consumos-->
            @foreach ($articulos as $articulo)

                <!-- FOREACH CONSUMOS: repasa cada uno de los consumos-->
                @foreach($consumos as $consumo_articulo)
                    <!-- Condición para designar correctamente los consumos con sus respectivos artículos y oficinas-->
                    @if ($consumo_articulo->clave_articulo == $articulo->clave && 
                        $consumo_articulo->id_oficina == $oficina->id)
                        <tr>
                        <td>{{$articulo->clave}}</td>
                        <td colspan="2">{{$articulo->nombre_articulo}}</td>
                        @php
                            $articulos_p_oficina++;
                            $articulos_p_depto++;
                        @endphp
                        
                        <!-- FOR repasa cada uno de los periodos del año y suma los consumos si son del mismo artículo en el mismo periodo--> 
                        @for ($x=1; $x<=12; $x++)
                            @foreach($consumos as $consumo)
                                @if ($consumo->clave_articulo == $articulo->clave && 
                                    $consumo->id_oficina == $oficina->id  &&
                                    $consumo->no_mes == $x)
                                    @php
                                        $acumulador_consumo += $consumo->cantidad;
                                        //print($acumulador_consumo);
                                        $total_p_articulo += $consumo->cantidad;                    
                                    @endphp
                                    
                                @endif
                            @endforeach

                            <td>{{$acumulador_consumo}}</td>

                            @php
                                $acumulador_consumo = 0;
                            @endphp

                        @endfor
                        
                        <td>{{$total_p_articulo}}</td>

                        @php
                            $total_p_articulo =0;
                        @endphp
                        </tr>
                        <!-- Una vez que repasó un artículo, se rompe el ciclo dentro del IF. Esto es para que no se repitan artículos --> 
                        @break
                    @endif
                @endforeach
                <!-- TERMINA FOREACH CONSUMOS -->
            @endforeach
            <!-- TERMINA FOREACH ARTICULOS -->
            <tr>
                <td>{{$articulos_p_oficina}}</th>
                <th>TIPOS DE ARTICULOS</th>
                <th>ARTÍCULOS POR OFICINA</th>
                @for ($x=1; $x<=12; $x++)
                    @foreach ($totales_p_consumo as $total_p_consumo)
                        @if ($total_p_consumo->id_oficina == $oficina->id && $total_p_consumo->no_mes == $x)
                            @php
                                $total_periodo[$x] = $total_periodo[$x] + $total_p_consumo->suma; 
                                $total_p_oficina += $total_p_consumo->suma;
                                $total_p_depto[$x] = $total_p_depto[$x] + $total_p_consumo->suma;
                            @endphp
                        @endif
                    @endforeach
                    @php
                        //dd($total_periodo);
                    @endphp
                    <td>{{$total_periodo["$x"]}}</td>
                @endfor

                <td>{{$total_p_oficina}}</td>
            </tr>
        @endif
    @endforeach
    <!-- TERMINA FOREACH OFICINA -->
    <tr>
        <td colspan="16"></td>
        <td colspan="16"></td>
    </tr>
    <tr>
        <td>{{$articulos_p_depto}}</td>
        <th>TIPOS DE ARTICULOS</th>
        <th>ARTÍCULOS POR UBPP</th>
    
    @for ($x=1;$x<=12;$x++)
        @php
            $total_suma_depto += $total_p_depto[$x];
        @endphp
        <td>{{$total_p_depto["$x"]}}</td>
    @endfor
        <td>{{$total_suma_depto}}</td>
    </tr>
@endforeach
<!-- TERMINA FOREACH DEPARTAMENTOS -->
@php
    $contador_articulos=0;
    $totales_p_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0);
    $total_primer_sem=0;
    $total_segundo_sem=0;
@endphp
<tr>
    <td colspan="16" style="border-bottom: 2px solid #dee2e6"></td>
</tr>
<tr>
    @foreach($articulos as $articulo)
        @php
            $contador_articulos++;
        @endphp
    @endforeach

    <td>{{$contador_articulos}}</td>
    <th>TIPOS DE ARTICULOS</th>
    <th>ARTÍCULOS POR MES</th>

    @for ($x=1;$x<=12;$x++)
        @foreach($totales_p_consumo as $total_p_consumo)
            @if($total_p_consumo->no_mes == $x)
                @php
                    $totales_p_mes[$x] += $total_p_consumo->suma;
                    $totales_p_mes[13] += $total_p_consumo->suma;
                @endphp
            @endif
            @if ($x<=6)
                @php
                    $total_primer_sem += $total_p_consumo->suma;
                @endphp
            @else
                @php
                    $total_segundo_sem += $total_p_consumo->suma;
                @endphp
            @endif
        @endforeach
        <td>{{$totales_p_mes["$x"]}}</td>
    @endfor
    <td>{{$totales_p_mes["13"]}}</td>
</tr>


<tr>
    <th colspan="2">TOTALES PRIMER SEMESTRE:</th>
    <td>{{$total_primer_sem}}</td>
    <th colspan="2">TOTALES SEGUNDO SEMESTRE:</th>
    <td>{{$total_segundo_sem}}</td>
    <th colspan="2">TOTALES ANUALES:</th>
    <td>{{$totales_p_mes["13"]}}</td>
</tr>

</tbody>
</table>

@endsection