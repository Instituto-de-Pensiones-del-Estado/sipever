@extends('almacen.reportes.encabezado_reporte')
@section('content')
    <style>
    thead{
        font-size:10px;
        border-bottom: 2px solid #dee2e6;
        
    }
    </style>
    <table>
    <thead>
        <tr>
          @foreach($headers as $header)
              <th style="white-space: normal;">{{$header}}</th>
          @endforeach
        </tr>
      </thead>
    <tbody style="font-size:10px;">
    
        @foreach ($consumos as $validConsumos)
            <tr>
                <td scope= "row">{{$validConsumos->folio}}</td>
                <td>{{$validConsumos->ubpp}}</td>
                <td>{{$validConsumos->clave}}</td>
                <td>{{$validConsumos->descripcion}}</td>
                <td>{{$validConsumos->descripcion_corta}}</td>
                <td>{{$validConsumos->cantidad}}</td>
                <td>{{$validConsumos->precio_unitario}}</td>
                <td>{{$validConsumos->subtotal}}</td>
            </tr>
        @endforeach
    </tbody>
    </table>
@endsection


