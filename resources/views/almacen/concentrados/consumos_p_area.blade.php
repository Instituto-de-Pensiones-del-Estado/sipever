@extends('almacen.reportes.encabezado_reporte')
@section('content')

<table border="1">   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
    </thead>

<tbody>
<!-- FOREACH Deptos: comienza la división por departamentos (oficina == 0) -->
@foreach ($deptos as $depto)
    <tr>
        <th>UBPP:{{$depto->ubpp}}</th>
        <th>DEPARTAMENTO:</th>
        <th>{{$depto->descripcion}}</th>
        <th colspan="13"></th>
    </tr>

    <!-- FOREACH Oficinas: comienza la división por oficinas (oficinas != 0) -->
    @foreach ($oficinas as $oficina)
        @if ($oficina->ubpp == $depto->ubpp)
            <tr>
                <th>OFICINA:</th>
                <th>{{$oficina->oficina}}</th>
                <th>{{$oficina->nombre_oficina}}</th>
                <th colspan="13"></th>
            </tr>
            <!-- FOREACH Artículos: comienza a exponerse los consumos por cada uno de los artículos que el depto y oficina correspondiente consumieron -->
            @foreach ($consumos as $consumo)

            @endforeach
        @endif
        

    @endforeach
@endforeach

@endsection