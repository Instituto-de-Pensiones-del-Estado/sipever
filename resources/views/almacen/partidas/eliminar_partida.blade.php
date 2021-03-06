<div class="modal fade" id="eliminarPartida" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="eliminarPartidaLabel" aria-hidden="true">
	<div class="modal-dialog articulo-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="eliminarPartidaLabel">Eliminar Partida</h3>
			</div>
			<form action="{{route('almacen.partidas.destroy')}}" method="post" accept-charset="utf-8">
				<div class="modal-body">
					<h5>Para eliminar la partida {{$partida->nombre}} debe reasignar sus artículos a otra partida</h5>
					<select name="nombre" class="col-sm-6 form-control" dir="ltr" id="articuloGrupo" required >
	                    <option value="">Seleccione una partida...</option>
	                    @foreach($partidas as  $partida_aux)
	                        @if($partida_aux_2->nombre != $partida->nombre)
	                        	<option value="{{$partida_aux_2->nombre}}">{{$partida_aux_2->nombre}}</option>
	                        @endif
	                    @endforeach
	                </select>
				</div>
				<div class="modal-footer">
	                <div class="col-md-5 colm-form-btns pull-right">
	                    <div class="pull-right">
	                        <button type="button" id="btn-cancelar" data-dismiss="modal" class="btn btn-cancel">Cancelar</button>
	                        <button type="submit" id="btn-guardar" class="btn btn-submit">Eliminar Partida</button>
	                    </div>
	                </div>
	            </div>
			</form>
		</div>
	</div>
</div>