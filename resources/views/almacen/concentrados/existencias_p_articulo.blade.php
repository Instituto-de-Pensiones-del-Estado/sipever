@extends('almacen.reportes.encabezado_reporte')
@section('content')
<table>
	<thead>
		<tr>
		@foreach($headers as $header)
			<th style="white-space: normal; padding-top: 20px">{{$header}}</th>
		@endforeach
		</tr>
	<tbody>
		@php
			$existen = 0;
			$total_articulos_mes = 0;
			$total_articulos_anio = 0;
			$total_partida = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
			$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);

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
								<!-- Si el mes esta abierto o cerrado, en caso de que se ocupe un mes abierto-->
									@if ($estatus_mes[0]->no_mes == $i)
										<td>{{$articulo_inf->existencias}}</td>
										<!--{{$existen = 1}}-->
										<!--{{$total_partida[$i] += $articulo_inf->existencias}} -->
										<!--{{$total_mes[$i] += $total_partida[$i]}}-->
									@else
										<td>{{$existencia_art->existencias}}</td>
										<!--{{$existen = 1}}-->
										<!--{{$total_partida[$i] += $articulo_inf->existencias}}-->
										<!--{{$total_mes[$i] += $total_partida[$i]}}-->
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
							<td>0</td>
						@endfor
					</tr>
				@endif
			@endforeach
			@php
				$total_partida = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);	
			@endphp
			<tr>
				<td colspan="3" style="text-align: right"> TOTAL DE PARTIDAS: </td>
				@for ($i = 1; $i < 12; $i++)
					<td> {{$total_partida[$i]}} </td>
				@endfor
			</tr>
		@endforeach
		<tr>
			<td colspan="3" style="text-align: right; font-weight: bold"> TOTAL POR MES</td>
			@for ($i = 1; $i <= 12; $i++)
				<td style = "text-align: center; font-weight: bold"> 
					{{$total_mes[$i]}}
				</td>
			@endfor
		</tr>
	</tbody>
</table>
@endsection