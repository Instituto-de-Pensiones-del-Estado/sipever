@extends('almacen.index')
@section('secciones_almacen')
@if(Auth::user()->hasRole('almacen_admin') )
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5 margin-tb">
                <div class="row">
                    <h2 class=" col-sm-1 text-center text-nowrap fas fa-clipboard">
                        <span style="font-family: 'Roboto';">Reportes</span>
                    </h2>
                </div>
                <b>Administración de reportes de Almacén </b>
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
                <h4 class="pull-left nombre-ventana">Generación de reportes de almacén</h4>

                <div class="pull-right">
                    <a class ="icon-ref" style="padding-right: 10px;" href="{{route('almacen.index')}}" title="">
                        <h3 class="fas fa-home"></h3>
                    </a>
                </div>
            </div>
        </div>
        <p></p>
    </div>

    <div class = "container reportes-box">
        <div class="modal-loader" id="loader">
            <div class="sp-box">
                <div class="sp sp1"></div>
                <div class="sp sp2"></div>
                <div class="sp sp3"></div>
                <div class="sp sp4"></div>
            </div>
        </div>
        <form id="reportesForm" action="{{route('almacen.reportes.generar')}}" method="POST">
            @csrf
            @method("POST")
            <div class ="row justify-content-md-center">
                <div class="col col-lg-4">
                    <div>
                        <h4 class="separador">Reportes</h4>
                        <div class="form-check" style="display: flex;">
                            <label class="check-container">Reporte auxiliar de almacén
                              <input type="checkbox" class="checkReporte" type="checkbox" name="auxAlmacen" value="" id="reporteAuxAlmacen">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: flex;">
                            <label class="check-container">Reporte compras de almacén
                              <input type="checkbox" class="checkReporte" type="checkbox" name="compAlmacen" value="" id="reporteCompAlmacen">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: flex;">
                            <label class="check-container">Reporte de consumos por departamento
                              <input type="checkbox" class="checkReporte" type="checkbox" name="consDepto" value="" id="reporteConsDepto">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: inline-flex;">
                            <label class="check-container">Reporte final de existencias
                              <input type="checkbox" class="checkReporte" type="checkbox" name="existencias" value="" id="reporteValidacionConsumo">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: inline-flex;">
                            <label class="check-container">Reporte para validación de consumos
                              <input type="checkbox" class="checkReporte" type="checkbox" name="validConsumo" value="" id="reporteExistencias">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <h4 class="separador">Concentrados</h4>
                        <div class="form-check" style="display: inline-flex;">
                            <label class="check-container">Compras por artículo
                              <input type="checkbox" class="checkReporte" type="checkbox" name="compArticulo" value="" id="comprasArticulo">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: inline-flex;">
                            <label class="check-container">Consumos por área y por artículo
                              <input type="checkbox" class="checkReporte" type="checkbox" name="consAreaArt" value="" id="consumosAreaArt">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: flex;">
                            <label class="check-container">Consumos por artículo
                              <input type="checkbox" class="checkReporte" type="checkbox" name="consArticulo" value="" id="consumosArticulo">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="form-check" style="display: inline-flex;">
                            <label class="check-container">Existencias por artículo
                              <input type="checkbox" class="checkReporte" type="checkbox" name="existArticulo" value="" id="existenciasArticulo">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class ="col-lg-8">
                    <div class="row">
                    <!--
                        <div class ="container-fluid" style="margin-top: 5%;">
                            <select id="selectDepto" class="form-control" name="depto" method="post">
                                <option value ="">Seleccione un departamento</option>
                                @foreach($departamentos as $departamento)
                                    <option value="{{$departamento->ubpp}}">{{$departamento->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class ="container-fluid" style="margin-top: 5%;">
                            <select id="selectOficina" class="form-control" name="oficina">
                                <option value ="">Seleccione una oficina</option>
                            </select>
                        </div>-->
                    </div>
                    <div id="reporteSingleton" style="margin-bottom: 5%; margin-top: 2%;">
                        <div class="form-check">
                            <label class="check-container">Generar reporte de un solo mes
                              <input type="checkbox" type="checkbox" name="chckMes" value="" id="chckMes">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label id="lblMesInicio" class="col-lg-7 text-left" style="padding-right: 50px;">Mes inicial</label>
                            <div id="groupMesInicio" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%;">
                                <input id="inptMesInicio" name="numMesInicio" type="text" class="form-control" value="1" required style="padding-right: 15px;" readonly>
                                <div class="input-group-btn-vertical" style="margin-left: 10px;">
                                    <button id="mesIniIncrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-up"></i>
                                    </button>
                                    <button id="mesIniDecrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>

                            <label id="lblYearInicio" class="col-lg-7 text-left" style="padding-right: 50px;">Año inicio</label>
                            <select id="selectYearInicio" name="yearInicio" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%; text-align-last: right;" required>
                                <option value="" dir="ltr">Año...</option>
                                @foreach($years as $year)
                                <option value="{{ $year->anio }}" dir="ltr">{{$year->anio}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label id="lblMesFin" class="col-lg-7 text-left" style="padding-right: 50px;">Mes fin</label>
                            <div id="groupMesFin" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%;">
                                <input id="inptMesFin" name="mesFin" type="text" class="form-control" value="1" required style="padding-right: 15px;" readonly>
                                <div class="input-group-btn-vertical" style="margin-left: 10px;">
                                    <button id="mesFinIncrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-up"></i>
                                    </button>
                                    <button id="mesFinDecrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                            <label id="lblYearFin" class="col-lg-7 text-left" style="padding-right: 50px;">Año fin</label>
                            <select id="selectYearFin"  name="yearFin" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%; text-align-last: right;">
                                <option value="" dir="ltr">Año...</option>
                                @foreach($years as $year)
                                <option value="{{ $year->anio }}" dir="ltr">{{$year->anio}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row" style="padding-top: 40%;">
                        <div class="container-fluid">
                            <div class="pull-right">
                                <button id="genReporte" type = "submit" class="btn btn-submit">Generar reporte</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="{{ asset('js/almacen/reportes.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/almacen/oficinas-depto.js') }}"></script>
@elseif(Auth::user()->hasRole('almacen_oficinista'))
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-5 margin-tb">
                <div class="row">
                    <h2 class=" col-sm-1 text-center text-nowrap fas fa-clipboard">
                        <span style="font-family: 'Roboto';">Reportes</span>
                    </h2>
                </div>
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
                <h4 class="pull-left nombre-ventana">Generar reporte de consumos del departamento</h4>

                <div class="pull-right">
                    <a class ="icon-ref" style="padding-right: 10px;" href="{{route('almacen.index')}}" title="">
                        <h3 class="fas fa-home"></h3>
                    </a>
                </div>
            </div>
        </div>
        <p></p>
    </div>

    <div class = "container poliza-box">
        <div class="modal-loader" id="loader">
            <div class="sp-box">
                <div class="sp sp1"></div>
                <div class="sp sp2"></div>
                <div class="sp sp3"></div>
                <div class="sp sp4"></div>
            </div>
        </div>
        <form id="reportesForm" action="{{route('almacen.reportes.generar')}}" method="POST">
            @csrf
            @method("POST")
            <input type="hidden" name="consDepto" value="checked" id="reporteConsDepto">
            <div class ="row justify-content-md-center">
                <div class ="col-lg-12">
                    <div class="row" style="margin-bottom: 5%; margin-top: 2%;">
                        <div class="col-lg-6">
                            <label id="lblMesInicio" class="col-lg-7 text-left" style="padding-right: 50px;">Mes inicial</label>
                            <div id="groupMesInicio" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%;">
                                <input id="inptMesInicio" name="numMesInicio" type="text" class="form-control" value="1" required style="padding-right: 15px;" readonly>
                                <div class="input-group-btn-vertical" style="margin-left: 10px;">
                                    <button id="mesIniIncrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-up"></i>
                                    </button>
                                    <button id="mesIniDecrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>

                            <label id="lblYearInicio" class="col-lg-7 text-left" style="padding-right: 50px;">Año inicio</label>
                            <select id="selectYearInicio" name="yearInicio" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%; text-align-last: right;" required>
                                <option value="" dir="ltr">Año...</option>
                                @foreach($years as $year)
                                    <option value="{{ $year->anio }}" dir="ltr">{{$year->anio}}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-6">
                            <label id="lblMesFin" class="col-lg-7 text-left" style="padding-right: 50px;">Mes fin</label>
                            <div id="groupMesFin" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%;">
                                <input id="inptMesFin" name="mesFin" type="text" class="form-control" value="1" required style="padding-right: 15px;" readonly>
                                <div class="input-group-btn-vertical" style="margin-left: 10px;">
                                    <button id="mesFinIncrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-up"></i>
                                    </button>
                                    <button id="mesFinDecrement" class="btn btn-default" type="button">
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                            <label id="lblYearFin" class="col-lg-7 text-left" style="padding-right: 50px;">Año fin</label>
                            <select id="selectYearFin"  name="yearFin" class="input-group spinner col-md-5" style="margin-left: 20%; width: 39.5%; text-align-last: right;">
                                <option value="" dir="ltr">Año...</option>
                                @foreach($years as $year)
                                    <option value="{{ $year->anio }}" dir="ltr">{{$year->anio}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="reporteSingleton">
                        <div class="form-check">
                            <label class="check-container">Generar reporte de un solo mes
                              <input type="checkbox" type="checkbox" name="chckMes" value="" id="chckMes">
                              <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row" style="padding-top: 10%;">
                        <div class="container-fluid">
                            <div class="text-center" style="align-content: center;">
                                <button id="genReporte" type = "submit" class="btn btn-submit">Generar reporte</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="{{ asset('js/almacen/reportes.js') }}"></script>
   <script type="text/javascript" src="{{ asset('js/almacen/oficinas-depto.js') }}"></script>
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