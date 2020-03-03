@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
@foreach ($partidas as $itemPartida)
<tbody>
    <tr>
    <th>Partida:</th>
    <th scope="row">{{$itemPartida->sscta}}</th>
    <th colspan="4">{{$itemPartida->nombre}}</td>
    </tr> 
    @foreach ($articulos as $itemArticulo)
        @if ($itemArticulo->id_cuenta == $itemPartida->id)
        <tr>
            <td scope="row"> {{$itemArticulo->clave}} </td>
            <td>{{$itemArticulo->descripcion}}</td>
            <td>{{$itemArticulo->descripcion_corta}}</td>
            <td>{{$itemArticulo->existencias}}</td>
            <td>{{$itemArticulo->precio_unitario}}</td>
        </tr>

        @endif
    @endforeach
</tbody>
@endforeach
@endsection