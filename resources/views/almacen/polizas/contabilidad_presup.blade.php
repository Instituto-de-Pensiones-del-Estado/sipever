@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
    <tr>
      @foreach($headers as $header)
          <th style="white-space: normal; padding-top: 20px">{{$header}}</th>
      @endforeach
    </tr>
</thead>
<tbody>
    @foreach($polizas as $part)
        @if ($part->CUENTAP == 3103)
        <tr>
            <td>{{$part->CUENTAP}}</td>
            <td>{{$part->SUBCTAP}}</td>
            <td>{{$part->SSUBCTAP}}</td>
            <td>{{$part->CONCEPTO}}</td>
            <td>${{round($part->IMPORTE, 2)}}</td>
        </tr>
        @else
        <tr>
            <td>{{$part->CUENTAP}}</td>
            <td>{{$part->SUBCTAP}}</td>
            <td>{{$part->SSUBCTAP}}</td>
            <td>{{$part->CONCEPTO}}</td>
            <td colspan="2" align="right">${{round($part->IMPORTE, 2)}}</td>
        </tr>
        @endif
    @endforeach
    <tr>
        <td colspan="10" style="border-top: 2px solid #dee2e6"></td>
    </tr>
    <tr>
        <td colspan="4"  style="font-size: 12px; font-weight: bold;">
            IMPORTE TOTALES: 
        </td>
        <td style="font-size: 12px; font-weight: bold;">
            ${{round($total[0]->IMPORTE, 2)}}
        </td>
        <td  style="font-size: 12px; font-weight: bold;">
            ${{round($total[0]->IMPORTE, 2)}}
        </td>
    </tr>
</tbody>
<!-- sección de firmas -->
<div style="font-size: 12px; text-align: center; display: block;">
    <table class="table" style="text-align: center;">
      <tr>
        <td>
          <input style="width: 250px; margin-left: 15%; margin-top: 10%" type="text" class="signature" />
        </td>
        <td>
          <input style="width: 250px; margin-left: 15%; margin-top: 10%" type="text" class="signature" />
        </td>
      </tr>
      <tr>
        <td>
          <p><b>ING. CRESENCIANO DOMINGUEZ SANCHEZ</b></p>
          <p>REVISÓ</p>
        </td>
        <td>
          <p><b>C.P. MANUEL GUZMÁN TRUJILLO</b></p>
          <P>AUTORIZÓ</P>
        </td>
      </tr>
    </table>
</div>
<footer>
</footer>
@endsection