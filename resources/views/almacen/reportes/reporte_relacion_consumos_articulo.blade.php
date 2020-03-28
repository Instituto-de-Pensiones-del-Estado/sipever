@extends('almacen.reportes.encabezado_reporte')
@section('content')
@php
  $total_cantidad_articulo = 0;
  $total_importe_articulo = 0;
@endphp
<!-- CREACIÓN DE HEADERS DE ACUERDO AL REPORTE -->
<table border="0">   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <tbody>
    <!-- FOREACH ARTICULOS -->
    <!-- Por cada artículo que haya tenido consumo en el período... -->
    @foreach ($articulos as $articulo)
      
      <tr>
        <td style="border-bottom: 2px solid #dee2e6; border-top: 2px solid #dee2e6">{{$articulo->clave}}</td>
        <td style="border-bottom: 2px solid #dee2e6; border-top: 2px solid #dee2e6">{{$articulo->nombre}}</td>
        <td style="border-bottom: 2px solid #dee2e6; border-top: 2px solid #dee2e6"></td>
        <td style="border-bottom: 2px solid #dee2e6; border-top: 2px solid #dee2e6">{{$articulo->descripcion_corta}}</td>
        <td style="border-bottom: 2px solid #dee2e6; border-top: 2px solid #dee2e6" colspan="4"></td>
      </tr>

    <!-- FOREACH ARTICULOS -->
    <!-- Por cada consumo del artículo corrrespondiente... -->
      @foreach ($consumos_p_articulo as $consumo)
        @if($articulo->clave == $consumo->clave)
          <tr>
            <td colspan="2"></td>
            <td>{{$consumo->folio}}</td>
            <td></td>
            <td>{{$consumo->cantidad}}</td>
            <td>{{$consumo->precio_unitario}}</td>
            <td>{{$consumo->subtotal}}</td>
            <td>{{$consumo->descripcion}}</td>
          </tr>

          @php
            $total_cantidad_articulo += $consumo->cantidad;
            $total_importe_articulo += $consumo->subtotal;
          @endphp
        @endif
        
      @endforeach
      <!-- Se imprimen los totales por cada artículo y se reinician los contadores -->

      <tr>
        <td colspan="2" style="border-top: 2px solid #dee2e6"></td>
        <td colspan="2" style="border-top: 2px solid #dee2e6">TOTAL DE ARTICULOS:</td>
        <td style="border-top: 2px solid #dee2e6">{{$total_cantidad_articulo}}</td>
        <td style="border-top: 2px solid #dee2e6"></td>
        <td style="border-top: 2px solid #dee2e6">{{$total_importe_articulo}}</td>
        <td style="border-top: 2px solid #dee2e6"></td>
      </tr>
      <tr><td></td></tr>
      <tr><td></td></tr>
      @php
          $total_cantidad_articulo=0;
          $total_importe_articulo=0;
        @endphp
    @endforeach
    
    </tbody>
</table>

@php
  $total_movimientos=0;
  $t_movs_partida = 0;
  $t_arts_partida = 0;
  $t_importe_partida = 0
@endphp
<!-- TABLA RESUMEN POR PARTIDAS -->
<!-- La siguiente tabla es la suma de los consumos por partida y al final se muestran los totales de todos los consumos-->
<table border="0s">
    <thead>
        <tr>
            <th>TOTAL DE ARTÍCULOS POR PARTIDAS</th>
            <th>CONSUMOS(MOVIMIENTOS)</th>
            <th>CANTIDAD DE ARTÍCULOS</th>
            <th>IMPORTES TOTALES</th>
        </tr>
    </thead>
    <tbody>
        <!-- FOREACH PARTIDAS -->
        <!-- Se usa para imprimir los consumos (movmientos, catidades e importe) por partida --> 
        @foreach($partidas as $resumen_partidas)
            @foreach ($consumos_p_articulo as $contador_consumos)
                @if($resumen_partidas->id == $contador_consumos->id_cuenta)
                    @php
                        $t_movs_partida += 1;
                        $t_arts_partida += $contador_consumos->cantidad;
                        $t_importe_partida += $contador_consumos->subtotal;
                    @endphp
                @endif
            @endforeach
            <tr>
                <td>{{$resumen_partidas->sscta}} {{$resumen_partidas->nombre}}</td>
                <td>{{$t_movs_partida}}</td>
                <td>{{$t_arts_partida}}</td>
                <td>{{$t_importe_partida}}</td>
            </tr>

            @php
              $total_movimientos += $t_movs_partida;
              $t_movs_partida=0;
              $t_arts_partida=0;
              $t_importe_partida=0;
            @endphp
        @endforeach
        <!-- TERMINA FOREACH PARTIDAS -->
    <tr>
      <td style="border-top: 2px solid #dee2e6"><b>TOTAL CONSUMOS: </b></td>
      <td style="border-top: 2px solid #dee2e6"><b>{{$total_movimientos}}</b></td>
      <td style="border-top: 2px solid #dee2e6"><b>{{$total_articulos}}</b></td>
      <td style="border-top: 2px solid #dee2e6"><b>{{$total_importe}}</b></td>
    </tr>
    </tbody>
    
<!-- TERMINA TABLA RESUMEN POR PARTIDAS -->



<footer>
</footer>
@endsection