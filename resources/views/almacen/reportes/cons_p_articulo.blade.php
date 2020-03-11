@extends('almacen.reportes.encabezado_reporte')
@section('content')
<thead>
    <tr>
      @foreach($headers as $header)
          <th style="white-space: normal; padding-top: 20px">{{$header}}</th>
      @endforeach
    </tr>
<tbody>
@php
    $suma_art = 0;
    $p_total_anio = 0;
    $suma_periodo = 0;
    $t_suma_anio = 0;
    $primer_semestre = 0;
    $segundo_semestre = 0;
@endphp
@foreach ($total_art as $art)
    <tr>
    <td>{{$art->clave}}</td>
    <td>{{$art->descripcion}}</td>
    <td>{{$art->descripcion_corta}}</td>
    @for ($i = $numMesInicio; $i <= $mesFin; $i++)
        @foreach ($articulos as $itemArtC)
            @if ($art->id == $itemArtC->id && $itemArtC->no_mes == $i)
            <!--{{$suma_art = $suma_art + $itemArtC->cantidad}}-->  
            <!--{{$p_total_anio = $p_total_anio + $itemArtC->cantidad}}-->    
            @endif
        @endforeach
    <td>{{$suma_art}}</td>
    @php
        $suma_art = 0;
    @endphp
    @endfor
    @for ($i = $mesFin; $i <= 11; $i++)
        <td> 0 </td>
    @endfor
    <td>{{$p_total_anio}}</td>
    @php
        $p_total_anio = 0;
    @endphp
    </tr>
@endforeach
<tr>
    <td colspan="16"><hr></td>
</tr>
<tr>
    <td style="font-size: 13px; font-weight: bold;">{{ $t_tipos_art}}</td>
    <td style="font-size: 13px; font-weight: bold;">TIPOS DE ARTICULOS</td>
    <td style=" font-size: 13px; font-weight: bold; ">TOTALES POR MES: </td>

@for ($i = $numMesInicio; $i <= $mesFin; $i++)
        @foreach ($articulos as $itemArtCC)
            @if ($itemArtCC->no_mes == $i )
                <!--{{$suma_periodo = $suma_periodo + $itemArtCC->cantidad}}-->
            @endif
        @endforeach
    <!--{{$t_suma_anio = $t_suma_anio + $suma_periodo}}-->
    <td style="font-size: 13px; font-weight: bold;">{{$suma_periodo}}</td>
    @php
        $suma_periodo = 0;
    @endphp
@endfor
@for ($i = $mesFin; $i <= 11; $i++)
        <td style="font-size: 13px; font-weight: bold;"> 0 </td>
@endfor
<td style="font-size: 13px; font-weight: bold;">{{$t_suma_anio}}</td>
</tr>
<tr>
    <td colspan="16"><hr></td>
</tr>
<tr>
    <td colspan="4" style="font-size: 13px; font-weight: bold; padding-left: 50%; ">TOTAL DEL PRIMER SEMESTRE: </td>
    @for ($i = $numMesInicio; $i <= 6; $i++)
        @foreach ($articulos as $itemArtCC)
            @if ($itemArtCC->no_mes == $i )
                <!--{{$primer_semestre = $primer_semestre + $itemArtCC->cantidad}}-->
            @endif
        @endforeach
    @endfor
    <td style="font-size: 13px; font-weight: bold;">{{$primer_semestre}}</td>
    <td colspan="4" style="font-size: 13px; font-weight: bold;">TOTAL DEL SEGUNDO SEMESTRE:</td>
    @for ($i = 7; $i <= 12; $i++)
        @foreach ($articulos as $itemArtCC)
            @if ($itemArtCC->no_mes == $i )
                <!--{{$segundo_semestre = $segundo_semestre + $itemArtCC->cantidad}}-->       
            @endif
        @endforeach
    @endfor
    <td style="font-size: 13px; font-weight: bold;">{{$segundo_semestre}}</td>
</tr>
</tbody>
<footer>
</footer>
@endsection