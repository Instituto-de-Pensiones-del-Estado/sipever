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
            <td colspan="13"><hr></td>
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
            <td colspan="4" style="padding-right: 30%; text-align: left">{{$itemArticulo->descripcion}}</td>
            <td>{{$itemArticulo->descripcion_corta}}</td>
            <td>{{$itemArticulo->existencias}}</td>
            <td>{{$itemArticulo->precio_unitario}}</td>
            <td>{{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>
            <td>${{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>      
         </tr>
        @foreach ($total_consumos as $consumo)
            @if ($consumo->id_articulo == $itemArticulo->id)
                <tr>
                    <td colspan="3" style="padding-left: 30%">{{$consumo->descripcion}}</td>
                    <td style="padding-left: 5%">CONSUMO</td>
                    <td colspan="2" style="padding-right: 20%;  text-align: left">{{$consumo->folio}}</td>
                    <td>{{$consumo->cantidad}}</td>
                    <td>{{$consumo->precio_unitario}}</td>
                    <td>${{$consumo->subtotal}}</td>
                </tr>
            @endif
        @endforeach
        @foreach ($total_compras as $compra)
            @if ($compra->id_articulo == $itemArticulo->id)
                <tr>
                    <td colspan="6" style="padding-left: 35%;">COMPRA</td>
                    <td>{{$compra->cantidad}}</td>
                    <td>{{$compra->precio_unitario}}</td>
                    <td>${{$compra->subtotal}}</td>
                </tr>
            @endif
        @endforeach
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
<footer>
</footer>
@endsection