@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
	<tr>
		@foreach($headers as $header)
			<th style="white-space: nowrap; padding-top: 20px">{{$header}}</th>
	    @endforeach
	</tr>
@php
	$subtotal = 0;
	$totalGeneral = 0;
	$cantArt = 0;
	$cantArtTotal = 0;
@endphp
<tbody>
@foreach ($partidas as $itemPartida)
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
			<td>{{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>
				<!--{{$subtotal = $subtotal + $itemArticulo->existencias * $itemArticulo->precio_unitario}}
				{{$cantArt = $cantArt + $itemArticulo->existencias}}
				{{$totalGeneral = $totalGeneral + $itemArticulo->existencias * $itemArticulo->precio_unitario}}}
				{{$cantArtTotal = $cantArtTotal + $itemArticulo->existencias}}-->
         </tr>
        @endif
	@endforeach
		 <tr>
			<td colspan="3" style="font-size: 13px; font-weight: bold; padding-left: 75%"> Cantidad de Articulos: </td>
			<td style="font-size: 13px; font-weight: bold;">{{$cantArt}}</td>
			<td style="font-size: 13px; font-weight: bold;"> Subtotal: </td>
			<td style="font-size: 13px; font-weight: bold;">{{$subtotal}}</td>	
		 </tr>
		 <tr>
			 <td colspan="8"><hr></td>
		 </tr>
		@php
			$subtotal = 0;
			$cantArt = 0;
		@endphp
@endforeach
<tr>
	<td colspan="3" style="font-size: 13px; font-weight: bold; padding-left: 60%"> TOTAL GRAL. DE ARTICULOS : </td>
	<td style="font-size: 13px; font-weight: bold;">{{$cantArtTotal}}</td>
	<td style="font-size: 13px; font-weight: bold;"> TOTAL GENERAL: </td>
	<td style="font-size: 13px; font-weight: bold;">{{$totalGeneral}}</td>
</tr>
</tbody>
</thead>
@endsection