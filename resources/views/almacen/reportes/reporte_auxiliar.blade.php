@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal; padding-top: 20px">{{$header}}</th>
          @endforeach
        </tr>
@php
    $total_inv_final = 0;
    $subtotal_inicial = 0;
    $subtotal_compras = 0;
    $subtotal_consumos = 0;
    $precio_unit = 0;
    $cont_mov_compras = 0;
    $cont_mov_consumos = 0;
    $cont_cant_arti_cons = 0;
    $cont_cant_arti_comp = 0;
    $cont_cant_inicial = 0;
    $importe_final = 0;
    $band = false;
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
            <td>{{$itemArticulo->cant_inicial}}</td>
            <td>{{$itemArticulo->precio_unitario}}</td>
            <td>{{$itemArticulo->cant_inicial * $itemArticulo->precio_unitario}}</td>
            <td>${{$itemArticulo->existencias * $itemArticulo->precio_unitario}}</td>   
            <!--{{$cont_cant_inicial = $cont_cant_inicial + $itemArticulo->cant_inicial}}  
            {{$subtotal_inicial = $subtotal_inicial + ($itemArticulo->cant_inicial * $itemArticulo->precio_unitario)}} 
            {{$total_inv_final = $total_inv_final + ($itemArticulo->existencias * $itemArticulo->precio_unitario)}}-->
         </tr>
        @foreach ($total_consumos as $consumo)
            @if ($consumo->id_articulo == $itemArticulo->id)
                <tr>
                    <td colspan="3" style="padding-left: 30%">{{$consumo->descripcion}}</td>
                    <td style="padding-left: 5%">CONSUMO</td>
                    <td colspan="2" style="padding-right: 20%;  text-align: left">{{$consumo->folio}}</td>
                    <td>{{$consumo->cantidad}}</td>
                    <td>{{$consumo->precio_unitario}}</td>
                    <td>${{bcdiv($consumo->subtotal, '1', 2)}}</td>
                    <!--{{$band = true}}
                    {{$precio_unit = $consumo->precio_unitario }}
                    {{$cont_mov_consumos = $cont_mov_consumos + 1}}
                    {{$cont_cant_arti_cons = $cont_cant_arti_cons + $consumo->cantidad}}
                    {{$subtotal_consumos = $subtotal_consumos + $consumo->subtotal}}-->
                </tr>
            @endif
        @endforeach
        @foreach ($total_compras as $compra)
            @if ($compra->id_articulo == $itemArticulo->id)
                <tr>
                    <td colspan="6" style="padding-left: 38%;">COMPRA</td>
                    <td>{{$compra->cantidad}}</td>
                    <td>{{$compra->precio_unitario}}</td>
                    <td>${{bcdiv($compra->subtotal, '1', 2)}}</td>
                    <!--{{$band = true}}
                    {{$cont_mov_compras = $cont_mov_compras + 1}}
                    {{$cont_cant_arti_comp = $cont_cant_arti_comp + $compra->cantidad}}
                    {{$subtotal_compras = $subtotal_compras + $compra->subtotal}}-->
                </tr>
            @endif
        @endforeach

        @if ($band == true)
            <td scope="row"> {{$itemArticulo->clave}} </td>
            <td colspan="4" style="padding-right: 30%; text-align: left">{{$itemArticulo->descripcion}}</td>
            <td>{{$itemArticulo->descripcion_corta}}</td>
            <td style="font-weight: bold;">{{$itemArticulo->existencias}}</td>
            <td>{{$precio_unit}}</td>
            <td>{{$itemArticulo->existencias * $precio_unit}}</td>
               
        @endif
        
       
        @php
            $band = false;
        @endphp
        @endif
    @endforeach
@endforeach
<tr>
    <td colspan="13"><hr></td>
</tr>
    <tr>
        <td colspan="6" style="text-align: right; font-size: 13px; font-weight: bold; padding-left: 70% ">CANTIDAD DE MOVIMIENTOS</td>
        <td style="font-size: 13px; font-weight: bold; text-align: center">CANTIDAD DE ARTICULOS</td>
        <td colspan="2" style="font-size: 13px; font-weight: bold; text-align: center">IMPORTES</td>
        <td style="font-size: 13px; font-weight: bold; text-align: center">INVENTARIO FINAL</td>
    </tr>
<tr>
    <td colspan="13"><hr></td>
</tr>
<tr>
    <td colspan="6" style="text-align: left; font-size: 13px; font-weight: bold;">SUBTOTAL INVENTARIO FINAL: </td>
    <td style="font-weight: bold">{{$cont_cant_inicial}}</td>
    <td colspan="2" style="font-weight: bold; text-align: center">{{$subtotal_inicial}}</td>
</tr>
<tr>
    <td colspan="3" style="font-size: 13px; font-weight: bold; text-align: left">COMPRAS DEL MES: </td>
    <td style="text-align: right">+</td>
    <td colspan="2" style="text-align: center">{{$cont_mov_compras}}</td>
    <td style="text-align: center">{{$cont_cant_arti_comp}}</td>
    <td colspan="2" style="text-align: center; font-weight: bold;"> {{$subtotal_compras}}</td>
</tr>
<tr>
    <td colspan="3" style="text-align: left; font-size: 13px; font-weight: bold;">CONSUMOS DEL MES: </td>
    <td style="text-align: right">-</td>
    <td colspan="2" style="text-align: center">{{$cont_mov_consumos}}</td>
    <td style="text-align: center">{{$cont_cant_arti_cons}}</td>
    <td colspan="2" style="text-align: center; font-weight: bold;"> {{$subtotal_consumos}}</td>
</tr>
<tr>
    <td colspan="13"><hr></td>
</tr>
<tr>
    <td colspan="6" style="font-weight: bold; font-size: 13px; text-align: left">INVENTARIO FINAL CONSIDERANDO PRECIOS PROMEDIO: </td>
    <td style="font-weight: bold; text-align: center">
        {{($cont_cant_inicial + $cont_cant_arti_comp) - $cont_cant_arti_cons}}
    </td>
    <td colspan="2" style="font-weight: bold; text-align: center">
        {{$importe_final = ($subtotal_inicial + $subtotal_compras) - $subtotal_consumos}}
    </td>
    <td colspan="2" style="font-weight: bold; text-align: center">{{$importe_final}}</td>
</tr>
<tr>
    <td colspan="13" style="padding-left: 70%"><hr></td>
</tr>
<tr>
    <td colspan="9" style="font-weight: bold; font-size: 13px; text-align: right"> DIFERENCIA: </td>
    <td style="font-weight: bold; text-align: center">{{$importe_final - $importe_final }}</td>
</tr>
</tbody>
<footer>
</footer>
@endsection