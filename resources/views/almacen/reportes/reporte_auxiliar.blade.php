@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal; padding-top: 20px">{{$header}}</th>
          @endforeach
        </tr>
@php
    $totalGeneral = 0;
    $cantArtTotal = 0;
@endphp
<tbody>
@foreach ($partidas as $itemPartida)
    @foreach ($articulos as $itemAr)
        @if ($itemAr->id_cuenta == $itemPartida->id)
        <tr>
            <td colspan="8"><hr></td>
        </tr>
        <tr>
            <th>Partida:</th>
            <th scope="row">{{$itemPartida->sscta}}</th>
            <th colspan="4">{{$itemPartida->nombre}}</td>
            </tr> 
            @break
        @endif
	@endforeach
    @foreach ($articulos as $itemArticulo)
        @if ($itemArticulo->id_cuenta == $itemPartida->id)
        <tr>
            <td scope="row"> {{$itemArticulo->clave}} </td>
            <td>{{$itemArticulo->descripcion}}</td>
            <td>{{$itemArticulo->descripcion_corta}}</td>
            <td>{{$itemArticulo->existencias}}</td>
            <td>{{$itemArticulo->precio_unitario}}</td>
            <td>{{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>
            <td>${{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>      
         </tr>
        <!--{{$totalGeneral = $totalGeneral + $itemArticulo->existencias * $itemArticulo->precio_unitario}}}
            {{$cantArtTotal = $cantArtTotal + $itemArticulo->existencias}}-->
        @endif
    @endforeach
@endforeach
<tr>
    <td colspan="2" style="padding-left: 30%; font-size: 13px; font-weight: bold;">ARTICULOS</td>
    <td style="font-size: 13px; font-weight: bold;">IMPORTES</td>
    <td style="font-size: 13px; font-weight: bold;">INVENTARIO</td>
</tr>
<tr>
    <td style="font-size: 13px; font-weight: bold;"> TOTAL GENERAL: </td>
	<td style="font-size: 13px; font-weight: bold;">{{$cantArtTotal}}</td>
    <td style="font-size: 13px; font-weight: bold;">${{$totalGeneral}}</td>
    <td style="font-size: 13px; font-weight: bold;">${{$totalGeneral}}</td>
</tr>
<tr>
    <td style="font-size: 13px; font-weight: bold;">DIFERENCIA: </td>
    <td style="font-size: 13px; font-weight: bold;">{{$totalGeneral - $totalGeneral}}</td>
</tr>
</tbody>
@endsection