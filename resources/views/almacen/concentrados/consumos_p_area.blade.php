@extends('almacen.reportes.encabezado_reporte')
@section('content')

<table border="1">   
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

    <!-- FOREACH Oficinas comienza la división por oficinas (oficinas != 0) -->
    @foreach ($oficinas as $oficina)
        @if ($oficina->ubpp == $depto->ubpp)
            <tr>
                <th>OFICINA</th>
                <th>{{$oficina->oficina}}</th>
                <th>{{$oficina->nombre_oficina}}</th>
                <th colspan="13"></th>
            </tr>
            <!-- Se declaran los contadores y acumuladores para  -->
            @php
                $acumulador_consumo=0;
                $total_articulos =0;
                $total_neto =0;
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
                        
                        <!-- FOR repasa cada uno de los periodos del año y suma los consumos si son del mismo artículo en el mismo periodo--> 
                        @for ($x=1; $x<=12; $x++)

                            @foreach ($consumos as $consumo)
                                @if ($consumo->clave_articulo == $articulo->clave && 
                                    $oficina->id == $consumo->id_oficina &&
                                    $consumo->no_mes == $x)
                                    @php
                                        $acumulador_consumo =+ $consumo->cantidad;
                                        $total_articulos += $consumo->cantidad;
                                    @endphp
                                @endif
                            @endforeach
                        
                            <td>{{$acumulador_consumo}}</td>

                            @php
                                $acumulador_consumo = 0;
                            @endphp

                        @endfor

                        <td>{{$total_articulos}}</td>

                        @php
                            $total_neto += $total_articulos;
                            $total_articulos =0;
                        @endphp
                        </tr>
                        <!-- Una vez que repasó un artículo, se rompe el ciclo dentro del IF. Esto es para que no se repitan artículos --> 
                        @break
                    @endif
                @endforeach
            @endforeach
            
        @endif
    @endforeach
@endforeach
</tbody>
</table>

@endsection