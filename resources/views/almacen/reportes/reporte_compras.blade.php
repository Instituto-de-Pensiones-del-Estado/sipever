<style type="text/css">

    @page { margin: 25px 25px; }

    .tabla_datos td, th {
        font-size: 10px;
    }
</style>
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
</tbody>
@endforeach
@endsection

