@extends('almacen.index')

@section('secciones_almacen')
@if(Auth::user()->hasRole('almacen_admin') || Auth::user()->hasRole('almacen_capturista'))
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5 margin-tb">
                <div class="row">
                    <h2 class=" col-sm-1 text-center text-nowrap fas fa-boxes">
                        <span style="font-family: 'Roboto';">Artículos</span>
                    </h2>
                </div>
                <b>Administración y Registro de artículos de Almacén </b>
            </div>
            <div class="col-md-6">
                @if ($message = Session::get('success'))
                    <div class="alert-container" id="contenedor-alert">
                        <div class="alert success">
                            <span class="closebtn">&times;</span>
                            <p id="test">{{ $message }}</p>
                        </div>
                    </div>
                @elseif ($errors->any())
                    <div class="alert-container" id="contenedor-alert">
                        <div class="alert alert-danger">
                            <span class="closebtn">&times;</span>
                            <strong>Error</strong>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </div>
                    </div>
                @elseif ($message = Session::get('warning'))
                    <div class="alert-container" id="contenedor-alert">
                        <div class="alert warning">
                            <span class="closebtn">&times;</span>
                            <p id="test">{{ $message }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 margin-tb header">
                <h4 class="pull-left nombre-ventana">Vista general de los artículos registrados</h4>

                <div class="pull-right">
                    <a class ="icon-ref" style="padding-right: 10px;" href="{{route('almacen.index')}}" title="">
                        <h3 class="fas fa-home"></h3>
                    </a>
                    @include('almacen.articulos.crear_articulo')
                    <a style="margin-bottom: 10px;" type="button" class="btn btn-agregar" data-toggle="modal" href="#createArticulo"> Agregar</a>
                </div>
            </div>
        </div>
        <p></p>
        <div class="row">
            <div class="panel" style="background-color: transparent;">
                <div class = "pull-left">
                    <form action="{{route('almacen.articulos.buscarArticulo')}}" method="POST">
                        @csrf
                        @method("POST")
                        <input name="nombreArticulo" class="input-busqueda"type="text" name="nombreArticulo" placeholder="Ingrese el nombre de un articulo a buscar">
                        <button type="submit"  class="btn btn-agregar">
                            <i class="fas fa-search"></i>
                            <span>Buscar</span>
                        </button>
                    </form>
                </div>
                <div class="pull-right">
                    <form action="{{route('almacen.articulos.buscarPartida')}}" method="POST">
                        @csrf
                        @method("POST")
                        <select name = "selectLista" id="buscarPorPartida" class="select-busqueda">
                            <option value="">Buscar articulo por grupo...</option>
                            <option value="Todos">TODOS LOS ARTICULOS</option>}
                            option
                            @foreach($grupos as  $partida)
                                <option value="{{$partida->nombre}}">{{$partida->nombre}}</option>
                            @endforeach
                        </select>
                        <button  type="submit" class="btn btn-agregar">
                            <i class="fas fa-search"></i>
                            <span>Buscar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-loader" id="loader">
        <div class="sp-box">
            <div class="sp sp1"></div>
            <div class="sp sp2"></div>
            <div class="sp sp3"></div>
            <div class="sp sp4"></div>
        </div>
    </div>
    <div class="row" style="margin-top: 1%;">
        <div class="col-md-12">
            <div class=" col-sm-5 desc-cuenta text-center" style="width: 39%;">
                <label>Descripción</label>
            </div>
            <div class="col-sm-2 text-nowrap" style="margin-right: -1%;">
                <label>Unidad de medida</label>
            </div>
            <div class="col-sm-2 text-center">
                <label>Estatus</label>
            </div>
            <div class="col-sm-3 desc-cuenta text-center">
                <label>Grupo</label>
            </div>
        </div>
    </div>
    <div class="panel-group menu-scroll" id="accordion" aria-multiselectable="true">
        @foreach ($articulos as $articulo)
            <div class="panel panel-menu">
                <div class="panel-heading titulo-panel">
                    <div class="panel-title" id="">
                        	<div class=" col-sm-5 desc-cuenta pull-left">
                        		{{ $articulo->descripcion }}
                        	</div>
                        	<div class="col-xs-1 text-nowrap"> {{ $articulo->descripcion_u_medida }}</div>
                    		@if($articulo->estatus == 0)
                                <div class="col-sm-2 de_baja text-nowrap text-center">
                                    De baja
                                </div>
                    		@elseif($articulo->existencias <= $articulo->stock_minimo)
                    			<div class="col-sm-2 stock_bajo text-nowrap text-center">
                    				Stock bajo
                    			</div>
                    		@elseif($articulo->existencias > $articulo->stock_minimo)
                                <div class="col-sm-2 en_stock text-nowrap text-center">
                                    En Stock
                                </div>
                    		@endif
                        	<div class="col-sm-3 desc-cuenta text-nowrap" style="margin-left: 2%;">{{$articulo->descripcion_cuenta}}</div>
                        	<div class="pull-right">
                                <button class = "btn btn-collapse btn-edit" id="btn_editar" disabled="true">
                                    <i class="fas fa-pen">  </i>
                                </button>
                                @if(Auth::user()->hasRole('almacen_admin'))
                                    <button onclick="location.href='{{route('almacen.articulos.darBaja', $articulo->clave)}}'" class="btn btn-collapse btn-delete" id="btn_eliminar" disabled >
                                        <i class="fas fa-trash-alt">  </i>
                                    </button>
                                @endif
                                <button id="verArticulo" type="button" class="btn btn-left btn-collapse collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapseArticulo">
                                    <i id="iconoDesplegar" class="fas fa-caret-square-down desplegar"></i>
                                </button>

                        </div>
                    </div>
                </div>
                <div id="collapseArticulo" class="collapse panel-collapse ">
                    <form method="POST" action="{{route('almacen.articulos.actualizarArticulo')}}" class="articuloForm">
                        @csrf
                        @method("POST")
                        <div class="panel-body">
                            <div>
                                <div class="row">
                                    <label class="col-md-2" for="articuloclave">Clave</label>
                                    <label class="col-md-6 text-left colm-form" for="articuloDescripcion">Descripción</label>
                                    <label class="col-md-2 " style="margin-left: 20px;" for="articuloExistencias">Existencias</label>
                                    <label class="col-md-2 colm-form" for="articuloUnidad">Unidad</label>

                                </div>
                                <div class="row">
                                    <input name="clave" type="text" class="col-sm-1 colm-form form-control" id="articuloClave" placeholder="Clave" value="{{$articulo->clave}}" disabled="true" required>
                                    <input name="descripcion" type="text" class="col-md-6 colm-form form-control" id="articuloDescripcion" placeholder="Descripcion" value="{{$articulo->descripcion}}" disabled="true">
                                    <input name="existencias" type="text" class="col-sm-1 colm-form-md form-control" id="articuloExistencias" placeholder="Existencias" value="{{$articulo->existencias}}" disabled="true" required readonly>
                                    <select name="unidad" class="col-sm-2 colm-form form-control" dir="ltr" id="articuloUnidad" disabled required>
                                        <option>{{$articulo->descripcion_u_medida}}</option>
                                        @foreach($unidades as $unidad)
                                            @if($unidad->descripcion_corta != $articulo->descripcion_u_medida)
                                                <option value="{{$unidad->descripcion_corta}}">{{ $unidad->descripcion_corta }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <label class="col-md-2" for="articuloStock">Stock Min.</label>
                                    <label class="col-sm-2" style="margin-left: 10px;" for="articuloPrecio">Precio</label>
                                    <label class="col-sm-1 colm-form-md" for="articuloEstatus">Estatus</label>
                                    <label class="col-sm-2 colm-form-md text-center" for="articuloGrupo">Grupo</label>
                                </div>
                                <div class="row">
                                    @if(Auth::user()->hasRole('almacen_admin'))
                                        <input name="stock_minimo" type="text" class="col-md-2 colm-form-md form-control" id="articuloStock" placeholder="Stock Min" value="{{$articulo->stock_minimo}}" disabled="true" required>
                                    @else
                                        <input name="stock_minimo" type="text" class="col-md-2 colm-form-md form-control" id="articuloStock" placeholder="Stock Min" value="{{$articulo->stock_minimo}}" disabled="true" required readonly>
                                    @endif
                                    <input name="precio_unitario" type="text" class="col-sm-1 colm-form-md form-control" id="articuloPrecio" placeholder="Precio" value="{{$articulo->precio_unitario}}" readonly required>
                                    @if($articulo->estatus == 0)
                                        <input type="text" class="col-sm-2 colm-form-md form-control" id="articuloEstatus" value="De baja" placeholder="Estatus" disabled="true" readonly>
                                    @elseif($articulo->existencias > $articulo->stock_minimo)
                                        <input type="text" class="col-sm-2 colm-form-md form-control" name="" id="articuloEstatus" value="En stock" placeholder="Estatus" disabled="true" readonly>
                                    @elseif($articulo->existencias == $articulo->stock_minimo)
                                        <input type="text" class="col-sm-2 colm-form-md form-control" name="" id="articuloEstatus" value="Stock bajo" placeholder="Estatus" disabled="true" readonly>
                                    @endif
                                    <select name="partida" class="col-sm-6 colm-form form-control" dir="ltr" id="articuloGrupo" required disabled>
                                        <option value="{{$articulo->descripcion_cuenta}}">{{$articulo->descripcion_cuenta}}</option>
                                        @foreach($grupos as  $partida)
                                            @if($partida->nombre != $articulo->descripcion_cuenta)
                                                <option value="{{$partida->nombre}}">{{$partida->nombre}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <div class="col-md-5 colm-form-btns pull-right">
                                        <div class="pull-right">
                                            <button type="button" id="btn-cancelar" class="btn btn-cancel" disabled="true">Cancelar</button>
                                            <button type="submit" id="btn-guardar" class="btn btn-submit" disabled="true">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="pull-right">
        @if($index > 0)
            <a class = "icon-ref" href="{{ route('almacen.articulos.next_page', $index-1) }}" title="">
                <h3 class="fas fa-chevron-circle-left"></h3>
            </a>
        @endif
        @if($no_partida == 0)
            <a class="icon-ref" href="{{ route('almacen.articulos.next_page', $index+1) }}" title="">
                <h3 class="fas fa-chevron-circle-right"></h3>
            </a>
        @endif
    </div>
    <script type="text/javascript" src="{{ asset('js/almacen/articulos.js') }}"></script>
@else
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5 margin-tb">
                <div class="row">
                    <h2 class=" col-sm-1 text-center text-nowrap fas fa-box-open">
                        <span style="font-family: 'Roboto';">Sin acceso</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 margin-tb header">
                <h3 class="pull-left nombre-ventana">Porfavor inicie sesión con una cuenta autorizada para tener acceso a este módulo</h3>
            </div>
        </div>
        <p></p>
    </div>
@endif
@endsection