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
    @foreach($partidasCompras as $partC)
        <tr>
            <td>{{$partC->cta}}</td>
            <td>{{$partC->scta}}</td>
            <td>{{$partC->sscta}}</td>
            <td>{{$partC->nombre}}</td>
            @foreach ($total_subtotales as $total_partida)
                @if ($total_partida->sscta == $partC->sscta)
                    <td> ${{$total_partida->sum_subtotal}}</td>                    
                @endif
            @endforeach
        </tr>
    @endforeach
    <tr>
        <td colspan="10" style="border-top: 2px solid #dee2e6"></td>
    </tr>
    <tr>
        <td colspan="6" style="text-align: right; font-size: 12px; font-weight: bold;">
            $
            {{$total_subtotales_general[0]->sum_subtotal}}
        </td>
    </tr>
    <br>
    <tr>
        <td colspan="10" style="border-top: 2px solid #dee2e6"></td>
    </tr>
    @foreach ($deptosConsumos as $depto)
    <tr>
        <td>{{$depto->ubpp}}</td>
        <td>{{$depto->scta}}</td>
        <td>{{$depto->sscta}}</td>
        <td>{{$depto->descripcion}}</td>
        @foreach ($total_subtotalesConsumos as $total_depto)
            @if ($total_depto->sscta == $depto->sscta)
                <td> ${{$total_depto->sum_subtotal}}</td>                    
            @endif
        @endforeach
    </tr>
    @endforeach
    <tr>
        <td colspan="7" style="border-top: 2px solid #dee2e6"></td>
    </tr>
    <tr>
        <td colspan="7" style="text-align: right; font-size: 12px; font-weight: bold;">
            $
            {{$total_subtotalesConsumos_general[0]->sum_subtotal}}
        </td>
    </tr>
</tbody>

<thead>
    <tr>
      @foreach($headers2 as $header2)
        @if ($header2=='DEPARTAMENTOS')
        <th style="white-space: normal; padding-top: 20px; text-align: left" colspan="4">{{$header2}}</th>
        @else
        <th style="white-space: normal; padding-top: 20px">{{$header2}}</th>
        @endif
      @endforeach
    </tr>
</thead>
<tbody>
    @foreach($partidasConsumos as $partCons)
        <tr>
            <td>{{$partCons->cta}}</td>
            <td>{{$partCons->scta}}</td>
            <td>{{$partCons->sscta}}</td>
            <td>{{$partCons->nombre}}</td>
            @foreach ($total_subtotalesPCons as $total_partidaCons)
                @if ($total_partidaCons->sscta == $partCons->sscta)
                    <td colspan="2" align="right"> 
                        $
                        {{$total_partidaCons->sum_subtotal}}
                    </td>                    
                @endif
            @endforeach
        </tr>
    @endforeach
        <tr>
            <td colspan="7" style="text-align: right; font-size: 12px; font-weight: bold;">
                ${{$total_subtotalesPCons_general[0]->sum_subtotal}}
            </td>
        </tr>
    <tr>
        <td colspan="10" style="border-top: 2px solid #dee2e6"></td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: right; font-size: 12px; font-weight: bold;" >
           IMPORTE TOTAL: 
        </td>

        <td style="text-align: right; font-size: 12px; font-weight: bold;" >
            $
            {{$abonos = $total_subtotalesConsumos_general[0]->sum_subtotal + $total_subtotales_general[0]->sum_subtotal}}
        </td>
        <td style="text-align: right; font-size: 12px; font-weight: bold;">
            $
            {{$cargos = $total_subtotalesConsumos_general[0]->sum_subtotal + $total_subtotales_general[0]->sum_subtotal}}
        </td>
        <td style="text-align: right; font-size: 12px; font-weight: bold;">
            $
            {{$abonos - $cargos}}
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