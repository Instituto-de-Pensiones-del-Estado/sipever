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
            <th scope="row"> {{$itemArticulo->clave}} </th>
            <th>{{$itemArticulo->descripcion}}</th>
            <th>{{$itemArticulo->no_factura}}</th>
            <th>{{$itemArticulo->descripcion_corta}}</th>
            <th>{{$itemArticulo->cantidad}}</th>
            <th>{{$itemArticulo->precio_unitario}}</th>
            <th>{{$itemArticulo->subtotal}}</th>
        </tr>

        @endif
    @endforeach
</tbody>
@endforeach
@endsection

