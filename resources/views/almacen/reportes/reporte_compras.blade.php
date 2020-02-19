<style type="text/css">

    @page { margin: 25px 25px; }

    .tabla_datos td, th {
        font-size: 10px;
    }
</style>
@extends('almacen.reportes.encabezado_reporte')
@section('content')


    <table class="tabla_datos">
    @foreach ($consulta as $object)
    <tr>

        <td>{{ $object->sscta }}</td>

        <td>{{ $object->nombre }}</td>

        <td>{{ $object->clave }}</td>

        <td>{{ $object->descripcion }}</td>

        <td>{{ $object->no_factura }}</td>

        <td>{{ $object->descripcion_corta }}</td>

        <td>{{ $object->cantidad }}</td>

        <td>{{ $object->precio_unitario }}</td>

        <td>{{ $object->subtotal }}</td>

        <br>

    </tr>
    @endforeach
    
    </table>

@endsection

