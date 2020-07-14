@extends('adminlte::layouts.landing')

@section('style')

{!! Html::style('components/bootstrap-table/dist/bootstrap-table.css') !!}

<style>  
  /*.img{
		width:80px;
  	height:80px;
	}*/

  
    .boton{
		width:150px;
  		height:150px;
		border: none;
		background: #00c375;
		color: #f2f2f2;
		border-radius: 500px;
		position: relative;
	}
	.boton:hover{
		border: none;
		opacity: 0.50;
	    -moz-opacity: .50;
	    filter:alpha (opacity=50);
	}
	button{
		outline:none;
	}
    
  .img{
		  width:50px;
  		height:50px;
	}

</style>

@endsection

@section('content')

<div class="row">



    <div class="col-md-1"></div> <!-- ./ col-md-1 -->
    
    <div class="col-md-10">
   
   

    <div class="box box-widget widget-user-2">

      <!--CABECERA -->
      <div class="widget-user-header bg-white" style="background: url('../img_system/greca_larga.png'); width: 1397px; height:136px;">
   
     
        <div class="widget-user-image">
          {{ HTML::image('components/admin-lte/dist/img/avatar5.png', 'User Avatar', array('class' => 'img-circle')) }}
        </div>
        <h1 class="widget-user-username">
          
          <code class="pull-left" style="color:#49453C">
            ING. SERGIO ARGENIS CASTILLO BRAVO
            <br>
            
            <div style="font-size:14px; font-family:Verdana, Geneva, sans-serif;">
            JEFE DE OFICINA DE GOBIERNO ELECTRÓNICO Y DESARROLLO DE APLICACIONES
            </div>

          </code>
          
        </h1>
        <br>
        <br>
       
        <h1 style="font-size:24px; font-family:Verdana, Geneva, sans-serif;">
          <code class="pull-right" style="color:#49453C">
            TECNOLOGÍAS DE LA INFORMACIÓN
          </code>
          
        <h1>
          
        <h5 class="widget-user-desc"></h5>
      </div><!-- /. widget-user-header bg-yellow -->

      <!--COLLAPSE DE FORMATOS DE PERMISOS -->     
      <div class="box box-solid">
          <div class="box-body">
            <div class="box-group" id="accordion">

              <!-- PRIMER COLLAPSE DE OBJETIVOS-->
              <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                       <div style="color:#3B0B0B; font-size:24px;"> Objetivo</div>
                      </a>
                    </h4>
                </div>
                
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="box-body">
                      <p style="font-size:20px; text-align:justify;">Desarrollar e implementar los sistemas de información que requiere la Institución para automatizar sus procesos, 
                          además actualizar y mantener en buen funcionamiento aquellos que están en producción.</p>
                    </div>
                </div><!-- ./ collapseOne-->
              </div>  <!-- ./ panel box box-primary-->
              <!-- ./ PRIMER COLLAPSE DE OBJETIVOS-->

              <!-- SEGUNDO COLLAPSE DE FUNCIONES -->
              <div class="panel box box-default">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                    <div style="color:#3B0B0B; font-size:24px;"> Descargar Formatos</div>
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="box-body">
                    
                    <ul class="nav nav-tabs">
                      <li class="active"><a href="#tab_1" data-toggle="tab"><strong>PERMISOS</strong></a></li>
                      <li><a href="#tab_2" data-toggle="tab"><strong>REQUERIMIENTOS</strong></a></li>
                    </ul>
                    <br>

                      <div class="tab-content">
                        
                        <!-- PRIMER TAB-->
                        <div class="tab-pane active" id="tab_1">

                          <!-- PRIMERA LINEA DE FORMATOS DE PERMISOS -->  
                          <table class="table table-bordered">
                            <div class="timeline-body">   
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/ACTIVO_FIJO.pdf" target="_blank"><button class="boton" style="background: #F08080;"><img class="img" src="{!! url('') !!}/img_download/TI/activo_fijo.png"/><h5 style="font-size: 18px;"><strong>Activo <br>Fijo</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/ACTIVOS.pdf" target="_blank"><button class="boton" style="background: #556B2F;"><img class="img" src="{!! url('') !!}/img_download/TI/activos.png"/><h5 style="font-size: 18px;"><strong>Activos</strong></h5></button></a>
                              </div>  
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/ALMACEN.pdf" target="_blank"><button class="boton" style="background: #33CCCC;"><img class="img" src="{!! url('') !!}/img_download/TI/almacen.png" /><h4 style="font-size: 18px;"><strong>Almacén</strong></h4></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/ASUNTOS_JURIDICOS.pdf" target="_blank"><button class="boton" style="background: #FF9900;"><img class="img"  src="{!! url('') !!}/img_download/TI/juridico.png"/><h4 style="font-size: 18px;"><strong>Asuntos<br>Jurídicos</strong></h4></button></a>
                              </div>        
                            </div>
                          </table>
                          <br> 
                          <!-- ./ PRIMERA LINEA DE FORMATOS DE PERMISOS -->  

                          <!-- SEGUNDA LINEA DE FORMATOS DE PERMISOS -->  
                          <table class="table table-bordered">
                            <div class="timeline-body">
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/AUXILIARES_91.pdf" target="_blank"><button class="boton" style="background: #CC66CC;"><img class="img"  src="{!! url('') !!}/img_download/TI/auxiliares.png"/><h5 style="font-size: 18px;"><strong>Auxiliares<br>91´S</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/CONTABILIDAD.pdf" target="_blank"><button class="boton" style="background: #D2691E;"><img class="img"  src="{!! url('') !!}/img_download/TI/contabilidad.png"/><h5 style="font-size: 18px;"><strong>Contabilidad</strong></h5></button></a>
                              </div>   
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/CONTROL_ADEUDOS.pdf" target="_blank"><button class="boton" style="background: #663399;"><img class="img"  src="{!! url('') !!}/img_download/TI/control.png"/><h4 style="font-size: 18px;"><strong>Control de<br>Adeudos</strong></h4></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/EGRESOS.pdf" target="_blank"><button class="boton" style="background: #9ACD32;"><img class="img"  src="{!! url('') !!}/img_download/TI/nomina_ipe.png"/><h4 style="font-size: 18px;"><strong>Egresos</strong></h4></button></a>
                              </div>      
                            </div>
                          </table>
                          <br>   
                          <!-- ./ SEGUNDA LINEA DE FORMATOS DE PERMISOS -->  

                          <!-- TERCERA LINEA DE FORMATOS DE PERMISOS -->  
                          <table class="table table-bordered">
                            <div class="timeline-body"> 
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/NOM_PENSIONADOS.pdf" target="_blank"><button class="boton" style="background: #FF6347;"><img class="img"  src="{!! url('') !!}/img_download/TI/nomina.png"/><h5 style="font-size: 18px;"><strong>Nómina <br>Pensionados</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/NOMINA.pdf" target="_blank"><button class="boton" style="background: #008080;"><img class="img"  src="{!! url('') !!}/img_download/TI/egresos.png"/><h5 style="font-size: 18px;"><strong>Nómina<br>IPE</strong></h5></button></a>
                              </div>   
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/NOMINA.pdf" target="_blank"><button class="boton" style="background: #191970;"><img class="img" src="{!! url('') !!}/img_download/TI/prestamos.png"/><h4 style="font-size: 18px;"><strong>Prestámos</strong></h4></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/PRESUPUESTOS.pdf" target="_blank"><button class="boton" style="background: #696969;"><img class="img" src="{!! url('') !!}/img_download/TI/presupuestos.png"/><h4 style="font-size: 18px;"><strong>Presupuestos</strong></h4></button></a>
                              </div>       
                            </div>
                          </table>
                          <br>

                          <!-- ./ TERCERA LINEA DE FORMATOS DE PERMISOS --> 

                          <!-- CUARTA LINEA DE FORMATOS DE PERMISOS -->  
                          <table class="table table-bordered">
                            <div class="timeline-body"> 
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/REVISTA_BENEFICIARIOS.pdf" target="_blank"><button class="boton" style="background: #000000;"><img class="img" src="{!! url('') !!}/img_download/TI/rev_beneficiarios.png"/><h5 style="font-size: 18px;"><strong>Revista de <br>Beneficiarios</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/REVISTA_SUPERVIVENCIA.pdf" target="_blank"><button class="boton" style="background: #DC143C;"><img class="img" src="{!! url('') !!}/img_download/TI/rev_supervivencia.png"/><h5 style="font-size: 18px;"><strong>Revista<br>Superviviencia</strong></h5></button></a>
                              </div>   
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/REVISTA_INCAPACITADOS.pdf" target="_blank"><button class="boton" style="background: #FF1493;"><img class="img" src="{!! url('') !!}/img_download/TI/rev_incapacitados.png"/><h4 style="font-size: 18px;"><strong>Revista<br>Incapacitados</strong></h4></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/SOPORTE_TECNICO.pdf" target="_blank"><button class="boton" style="background: #7CFC00;"><img class="img"  src="{!! url('') !!}/img_download/TI/soporte.png"/><h4 style="font-size: 18px;"><strong>Soporte<br>Técnico</strong></h4></button></a>
                              </div>              
                            </div>
                          </table>
                          <br>
                          <!-- ./ CUARTA LINEA DE FORMATOS DE PERMISOS -->  

                          <!-- QUINTA LINEA DE FORMATOS DE PERMISOS -->  

                          <table class="table table-bordered">
                            <div class="timeline-body">  
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/SUELDO_BASE.pdf" target="_blank"><button class="boton" style="background: #800000;"><img class="img"  src="{!! url('') !!}/img_download/TI/sueldo.png"/><h5 style="font-size: 18px;"><strong>Sueldo <br>Base</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/permisos/TRAMITE_BENEFICIOS.pdf" target="_blank"><button class="boton" style="background: #FF9900"><img class="img"  src="{!! url('') !!}/img_download/TI/beneficios.png"/><h5 style="font-size: 18px;"><strong>Trámite de<br>Beneficios</strong></h5></button></a>
                              </div>     
                            </div>
                          </table>   


                        </div> <!-- ./ PRIMER TAB-->

                        <!-- SEGUNDO TAB-->
                        <div class="tab-pane" id="tab_2">
                          <table class="table table-bordered">
                            <div class="timeline-body">             
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/requerimientos/SISTEMAS_2019.doc" target="_blank"><button class="boton" style="background: #9933CC;"><img class="img" src="{!! url('') !!}/img_download/TI/sistemas.png"/><h5 style="font-size: 18px;"><strong>SISTEMAS<br>2019</strong></h5></button></a>
                              </div>
                              <div class="col-md-3">
                                <a href="{!! url('') !!}/files/TI/requerimientos/SISTEMAS_WEB_2019.doc" target="_blank"><button class="boton" style="background: #339999;"><img class="img" src="{!! url('') !!}/img_download/TI/sistemas_web.png"/><h5 style="font-size: 18px;"><strong>WEB 2019</strong></h5></button></a>
                              </div>
                            </div>
                          </table>
                        </div><!-- ./ SEGUNDO TAB-->

                      </div><!-- tab-content -->




                  </div><!-- box-body collapseTwo-->
                </div><!-- ./ collapseTwo-->
              </div> <!-- ./ panel box box-success-->

              <!-- ./ SEGUNDO COLLAPSE DE OBJETIVOS-->

             

            </div><!-- ./ box-group-->
          </div><!-- ./ box-body-->
      </div><!-- ./ box box-solid-->

    </div>  <!-- ./ box box-widget widget-user-2-->  
  
    </div> <!-- ./ col-md-10 -->
    <div class="col-md-1"></div> <!-- ./ col-md-1 -->
</div> <!-- ./ row -->


@endsection

@section('script')

@endsection

