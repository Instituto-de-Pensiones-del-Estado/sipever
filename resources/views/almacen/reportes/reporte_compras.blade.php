@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>

<tbody>        
<!-- Foreach de Partida -->       
@foreach ($partidas as $itemPartida)
    @foreach ($articulos as $itemAr)
        @if($itemAr->id_cuenta == $itemPartida->id)
        <tr>
            <td colspan="8"><hr></td>
        </tr>
        <tr>
            <th>Partida:</th>
            <th scope="row">{{$itemPartida->sscta}}</th>
            <td colspan="4">{{$itemPartida->nombre}}</td>
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
                    <td>{{$itemArticulo->descripcion}}</td>
                    <td>{{$itemArticulo->no_factura}}</td>
                    <td>{{$itemArticulo->descripcion_corta}}</td>
                    <td>{{$itemArticulo->cantidad}}</td>
                    <td>{{$itemArticulo->precio_unitario}}</td>
                    <td>{{$itemArticulo->subtotal}}</td>
                </tr>

                @endif
            @endforeach

        <!-- Contador de movimientos por partida -->
        @foreach($total_movimientos as $itemMovimientos) 
                @if($itemMovimientos->sscta == $itemPartida->sscta )
                    <th style="white-space: normal;">{{$itemMovimientos->count}} Movimientos</th>
                @endif 
        @endforeach

        <!-- Suma de cantidades por partida -->
        @foreach($total_cantidades as $itemCantidades) 
                @if($itemCantidades->sscta == $itemPartida->sscta )
                    <th style="white-space: normal;">ARTÍCULOS: {{$itemCantidades->sum_cantidad}}</th>
                @endif 
        @endforeach

        <!-- Suma de subtotales por partida -->
        @foreach($total_subtotales as $itemSubtotales) 
                @if($itemSubtotales->sscta == $itemPartida->sscta )
                    <th style="white-space: normal;">SUBTOTAL: {{$itemSubtotales->sum_subtotal}}</th>
                @endif 
        @endforeach

</tbody>  

         
@endforeach

<!-- Contador de movimientos generales -->
@foreach($total_movimientos_general as $itemMovimientosGeneral) 
    <th style="white-space: normal;">{{$itemMovimientosGeneral->count}} Movimientos</th>  
@endforeach

<!-- Suma de cantidades generales -->
@foreach($total_cantidades_general as $itemCantidadesGeneral) 
    <th style="white-space: normal;">ARTÍCULOS: {{$itemCantidadesGeneral->sum_cantidad}}</th>  
@endforeach

<!-- Suma de subtotales generales -->
@foreach($total_subtotales_general as $itemSubtotalesGeneral) 
    <th style="white-space: normal;">TOTAL: {{$itemSubtotalesGeneral->sum_subtotal}}</th>  
@endforeach

@endsection


