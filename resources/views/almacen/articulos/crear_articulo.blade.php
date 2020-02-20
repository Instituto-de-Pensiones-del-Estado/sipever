<div class="modal fade" id="createArticulo" data-backdrop="static" tabindex="-1" aria-labelledby="createArticuloLabel">
	<div class="modal-dialog articulo-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createArticuloLabel"> Agregar nuevo articulo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		         	<span aria-hidden="true">&times;</span>
		        </button>
			</div>
            <form action="{{route('almacen.articulos.nuevoArticulo')}}" method="POST" id="newArticulo">
    			<div class="modal-body">
    				<div class="container-fluid">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <label class="col-md-2" for="articuloclave">Clave</label>
                            <label class="col-md-6 text-left colm-form" for="articuloDescripcion">Descripción</label>
                            <label class="col-md-3 " for="articuloExistencias">Existencias</label>
                            <label class="col-md-2 colm-form" for="articuloUnidad">Unidad</label>

                        </div>
                        <div class="row">
                            <input name="clave" type="text" class="col-sm-1 colm-form form-control" id="articuloClave" placeholder="Clave" required>
                            <input name="descripcion" type="text" class="col-md-6 colm-form form-control" id="articuloDescripcion" placeholder="Descripcion">
                            <input name="existencias" type="text" class="col-sm-1 colm-form-md form-control" id="articuloExistencias" placeholder="Existencias"required>
                            <select name="unidad" class="col-sm-2 colm-form form-control" style="width: 100%;" dir="ltr" id="articuloUnidad" required>
                                @foreach($unidades as $unidad)
                                    <option value="{{$unidad->descripcion_corta}}">{{ $unidad->descripcion_corta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <label class="col-md-3 text-nowrap" for="articuloStock">Stock Min.</label>
                            <label class="col-md-3 text-nowrap" for="articuloStock">Stock Max.</label>
                            <label class="col-sm-2" style="margin-left: 15px;" for="articuloPrecio">Precio</label>
                            <label class="col-sm-2 colm-form-md text-center" for="articuloGrupo">Grupo</label>
                        </div>
                        <div class="row">
                            <input name="stock_minimo" type="text" class="col-md-3 colm-form-md form-control" id="articuloStock_min" placeholder="Stock min." required>
                            <input name="stock_maximo" type="text" class="col-md-3 colm-form-md form-control" id="articuloStock_max" placeholder="Stock max." required>
                            <input name="precio_unitario" type="text" class="col-sm-1 colm-form-md form-control" id="articuloPrecio" placeholder="Precio" required>
                            <select name="partida" class="col-sm-6 colm-form form-control" dir="ltr" id="articuloGrupo" required >
                                <option value="">Seleccione una partida...</option>
                                @foreach($grupos as  $partida)
                                    <option value="{{$partida->nombre}}">{{$partida->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
    			</div>
                <div class="modal-footer">
                    <div class="col-md-5 colm-form-btns pull-right">
                        <div class="pull-right">
                            <button type="button" id="btn-cancelar" data-dismiss="modal" class="btn btn-cancel" >Cancelar</button>
                            <button type="submit" id="btn-guardar" class="btn btn-submit">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
		</div>
	</div>
</div>