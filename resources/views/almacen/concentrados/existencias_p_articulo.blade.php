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
		$existen = 0;
	@endphp
	<!-- Este foreach sirve para obtener una partida de lista -->
	@foreach ($total_partidas as $partida)
		<tr>
			<td style="text-align: left; font-size: 13px; font-weight: bold;">PARTIDA: </td>
			<td style="text-align: left; font-size: 13px; font-weight: bold;">{{$partida->sscta}}</td>
			<td style="text-align: left; font-size: 13px; font-weight: bold;">{{$partida->nombre}}</td>
		</tr>
		<!-- Este foreach sirve para obtener la información de los articulos -->
		@foreach ($total_articulos as $articulo_inf)
			<!-- Este if es para verificar que el articulo pertenece a la partida e ingresar la información del articulo-->
			@if ($partida->id == $articulo_inf->id_cuenta)
				<tr>
					<td>{{$articulo_inf->clave}}</td>
					<td>{{$articulo_inf->descripcion}}</td>
					<td>{{$articulo_inf->descripcion_corta}}</td>
					<!-- Este for sirve para repasar los periodos del articulo del mes que inicio hasta el final -->
					@for ($i = $numMesInicio; $i <= $mesFin; $i++)
						<!-- revisamos las existencias de un articulo -->
						@foreach ($existencias_articulos as $existencia_art)
							@if ($existencia_art->no_mes == $i && $existencia_art->id == $articulo_inf->id)							
								@if ($estatus_mes[0]->no_mes == $i)
									<td>{{$articulo_inf->existencias}}</td>
									<!--{{$existen = 1}}-->
								@else
									<td>{{$existencia_art->existencias}}</td>
									<!--{{$existen = 1}}-->
								@endif
							@endif
						@endforeach
						@if ($existen == 0)
							<td>0</td>
						@endif
					@endfor
						@php
							$existen = 0;
						@endphp
					@for ($i = $mesFin; $i < 12; $i++)
						<td> 0 </td>
					@endfor
				</tr>
			@endif
		@endforeach
	@endforeach
</tbody>
<footer>
</footer>
@endsection