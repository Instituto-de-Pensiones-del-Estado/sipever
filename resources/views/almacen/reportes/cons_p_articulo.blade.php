@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
    <tr>
      @foreach($headers as $header)
          <th style="white-space: normal; padding-top: 20px">{{$header}}</th>
      @endforeach
    </tr>
<tbody>
@php
    $sumaArticulo = 0;
@endphp
@foreach ($articulos as $articulo)
    <tr>
    @switch($articulo->id_periodo)
        @case(1)
            @foreach ($articulos as $item)
                @if ($item->id == $articulo->id && $item->id_periodo == $articulo->id_periodo)
                    <!--{{$sumaArticulo = $sumaArticulo + $item->cantidad}}-->
                @endif
            @endforeach
            <td>{{$articulo->clave}}</td>
            <td>{{$articulo->descripcion}}</td>
            <td>{{$articulo->descripcion_corta}}</td>
            <td>
                {{$articulo->cantidad + $sumaArticulo}}
            </td>
            @php
                $sumaArticulo = 0;
            @endphp
            @break
        @case(2)
            @foreach ($articulos as $item)
                @if ($item->id == $articulo->id && $item->id_periodo == $articulo->id_periodo)
                    <!--{{$sumaArticulo = $sumaArticulo + $item->cantidad}}-->
                @endif
            @endforeach
            <td>{{$articulo->clave}}</td>
            <td>{{$articulo->descripcion}}</td>
            <td>{{$articulo->descripcion_corta}}</td>
            <td>
                {{$articulo->cantidad + $sumaArticulo}}
            </td>
            @php
                $sumaArticulo = 0;
            @endphp
        @break
        @default        
    @endswitch
    </tr>
@endforeach
</tbody>
@endsection