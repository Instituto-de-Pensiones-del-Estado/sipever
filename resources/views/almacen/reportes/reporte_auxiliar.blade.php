@extends('almacen.reportes.encabezado_reporte')
@section('content')
@foreach ($partidas as $itemPartida)
<tbody>
    <tr>
    <th>Partida:</th>
    <th scope="row">{{$itemPartida->sscta}}</th>
    <td colspan="4">{{$itemPartida->nombre}}</td>
    </tr> 
    @foreach ($articulos as $itemArticulo)
        @if ($itemArticulo->id_cuenta == $itemPartida->id)
        <tr>
            <th scope="row"> {{$itemArticulo->clave}} </th>
            <th>{{$itemArticulo->descripcion}}</th>
            <th>{{$itemArticulo->descripcion_corta}}</th>
            <th>{{$itemArticulo->existencias}}</th>
            <th>{{$itemArticulo->precio_unitario}}</th>
        </tr>

        @endif
    @endforeach
</tbody>
@endforeach
@endsection