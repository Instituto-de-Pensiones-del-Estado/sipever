@extends('almacen.reportes.encabezado_reporte')
@section('content')
<table>   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <tbody>
    @foreach ($deptos as $departamentos)
            <tr>
                <th>ÁREA: {{departamentos->ubpp}} {{departamentos->descripcion}}</th>
            </tr>
            @foreach ($partidas as $seccion_partidas)
                @foreach ($consumos as $validConsumos)
                    @if($seccion_partidas.id == $validConsumos.id_cuenta and $validConsumos.ubpp == $departamentos.ubpp)
                    <tr>
                        <td scope= "row">{{$validConsumos->folio}}</td>
                        <td>{{$validConsumos->ubpp}}</td>
                        <td>{{$validConsumos->clave}}</td>
                        <td>{{$validConsumos->descripcion}}</td>
                        <td>{{$validConsumos->descripcion_corta}}</td>
                        <td>{{$validConsumos->cantidad}}</td>
                        <td>{{$validConsumos->precio_unitario}}</td>
                        <td>{{$validConsumos->subtotal}}</td>
                    </tr>
                    @endif
                @endforeach
            @endforeach 
    @endforeach
        <th colspan="2">{{$total_consumos}} CONSUMOS</th>
        <th colspan="2">ARTÍCULOS: {{total_articulos}}</th>
        <th colspan="4">IMPORTE TOTAL: {{$total_importe}}</th>
    </tbody>
    </table>
    <footer>
    </footer>
@endsection