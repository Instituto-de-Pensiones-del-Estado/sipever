@extends('almacen.reportes.encabezado_reporte')
@section('content')

<table>   
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
    </thead>
@foreach ($periodos as $periodo)
    

@endforeach

@endsection