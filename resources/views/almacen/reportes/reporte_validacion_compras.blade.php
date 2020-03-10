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
        @foreach ($articulos as $itemArticulo)
                        <!-- Comparación del id de cuenta del articulo con id de la partida -->  
                        
          <tr>
             <td scope="row"> {{$itemArticulo->no_factura}} </td>
             <td>{{$itemArticulo->clave}}</td>
             <td> {{$itemArticulo->descripcion}}</td>
             <td>{{$itemArticulo->descripcion_corta}}</td>
             <td>{{ number_format($itemArticulo->cantidad) }}</td>
             <td>{{ number_format($itemArticulo->precio_unitario,2)}}</td>
             <td>{{ number_format($itemArticulo->subtotal,2) }}</td>
          </tr>

                      
        @endforeach
    

    </tbody>
</table>
<table>
    <tbody>
        <!-- Contador de movimientos generales -->    
        @foreach($total_movimientos_general as $itemMovimientosGeneral) 
            <td colspan="1"  style="font-size: 12px; font-weight: bold;">{{$itemMovimientosGeneral->count}} Compras</th>  
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