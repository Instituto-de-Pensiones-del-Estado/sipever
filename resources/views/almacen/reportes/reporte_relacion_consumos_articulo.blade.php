@extends('almacen.reportes.encabezado_reporte')
@section('content')
<!-- CREACIÓN DE HEADERS DE ACUERDO AL REPORTE -->
<table>   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <tbody>
    @foreach ($articulos as $articulo)
      <tr>
        <td>{{$articulo->clave}}</td>
        <td>{{$articulo->nombre}}</td>
        <td>{{$articulo->descripcion_corta}}</td>
      </tr>
      @foreach ($consumos_p_articulo as $consumo)
        @if($articulo->clave == $consumo->clave)
          <tr>
            <td></td>
          </tr>
        @endif
      @endforeach
    @endforeach
    
    </tbody>
</table>



<footer>
</footer>
@endsection