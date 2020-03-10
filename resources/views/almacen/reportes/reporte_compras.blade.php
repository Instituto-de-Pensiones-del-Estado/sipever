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
        <!-- Foreach de Partida -->       
        @foreach ($partidas as $itemPartida)
            @foreach ($articulos as $itemAr)
                @if($itemAr->id_cuenta == $itemPartida->id)
                <tr>
                    <td colspan="12"></td>
                </tr>
                <tr>
                    <th>Partida:</th>
                    <th scope="row">{{$itemPartida->sscta}}</th>
                    <td colspan="4" style="font-weight: bold;">{{$itemPartida->nombre}}</td>
                </tr> 
                @break
                @endif
            @endforeach  

                    <!-- Foreach de Articulos -->  
                    @foreach ($articulos as $itemArticulo)
                        <!-- Comparación del id de cuenta del articulo con id de la partida -->  
                        @if ($itemArticulo->id_cuenta == $itemPartida->id)
                        <tr>
                            <td scope="row"> {{$itemArticulo->clave}} </td>
                            <td> {{$itemArticulo->descripcion}}</td>
                            <td>{{$itemArticulo->no_factura}}</td>
                            <td>{{$itemArticulo->descripcion_corta}}</td>
                            <td>{{ number_format($itemArticulo->cantidad) }}</td>
                            <td>{{ number_format($itemArticulo->precio_unitario,2)}}</td>
                            <td>{{ number_format($itemArticulo->subtotal,2) }}</td>
                        </tr>

                        @endif
                    @endforeach

                <!-- Contador de movimientos por partida -->
                @foreach($total_movimientos as $itemMovimientos) 
                        @if($itemMovimientos->sscta == $itemPartida->sscta )
                            <th colspan="1" style="white-space: normal;">{{$itemMovimientos->count}} Movimientos</th>
                        @endif 
                @endforeach

                <!-- Suma de cantidades por partida -->
                @foreach($total_cantidades as $itemCantidades) 
                        @if($itemCantidades->sscta == $itemPartida->sscta )
                            <th colspan="1" style="white-space: normal;">ARTÍCULOS: {{ number_format($itemCantidades->sum_cantidad) }}</th>
                        @endif 
                @endforeach

                <!-- Suma de subtotales por partida -->
                @foreach($total_subtotales as $itemSubtotales) 
                        @if($itemSubtotales->sscta == $itemPartida->sscta )
                            <th colspan="2" style="white-space: normal;">SUBTOTAL: {{ number_format($itemSubtotales->sum_subtotal,2)}}</th>
                        @endif 
                @endforeach   
        @endforeach
    </tbody>
</table>

<table>
    <tbody>
        <!-- Contador de movimientos generales -->    
        @foreach($total_movimientos_general as $itemMovimientosGeneral) 
            <td colspan="1"  style="font-size: 12px; font-weight: bold;">{{$itemMovimientosGeneral->count}} Movimientos</th>  
        @endforeach

        <!-- Suma de cantidades generales -->
        @foreach($total_cantidades_general as $itemCantidadesGeneral) 
            <td colspan="1"  style="font-size: 12px; font-weight: bold;">ARTÍCULOS: {{ number_format($itemCantidadesGeneral->sum_cantidad) }}</th>  
        @endforeach

        <!-- Suma de subtotales generales -->
        @foreach($total_subtotales_general as $itemSubtotalesGeneral) 
            <td colspan="2"  style="font-size: 12px; font-weight: bold;">TOTAL: {{ number_format($itemSubtotalesGeneral->sum_subtotal,2) }}</th>  
        @endforeach

    </tbody>

</table>


<footer>
</footer>
@endsection





