@extends('almacen.reportes.encabezado_reporte')
@section('content')
<!-- CREACIÓN DE HEADERS DE ACUERDO AL REPORTE -->
<table border = "1">   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <!-- Inicializando acumulador de importe total -->
    @php
      $importe_total_final=0;
    @endphp
    <tbody>
    @foreach($deptos as $departamento)
      @foreach($consumos_p_depto as $consumo)
        @if($departamento->ubpp = $consumo->oficina_ubpp)
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