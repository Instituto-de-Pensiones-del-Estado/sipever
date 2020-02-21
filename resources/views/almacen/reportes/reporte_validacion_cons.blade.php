@extends('almacen.reportes.encabezado_reporte')
@section('content')
    <table>
    <tbody style="font-size:12px;">
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


