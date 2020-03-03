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
    <tbody>
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
        <th>{{$total_consumos}} CONSUMOS</th>
        <th>ARTÍCULOS: {{$total_articulos}}</th>
        <th>IMPORTE TOTAL: {{$total_importe}}</th>
    </tbody>
    </table>
    <footer>
    </footer>
@endsection


