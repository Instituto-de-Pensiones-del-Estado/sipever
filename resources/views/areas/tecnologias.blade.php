@extends('adminlte::layouts.landing')

@section('style')

{!! Html::style('components/bootstrap-table/dist/bootstrap-table.css') !!}

<style>  
  .img{
		width:80px;
  	height:80px;
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
            DR. ERICK SAMUEL GUTIÉRREZ RENDÓN
            <br>
            
            <div style="font-size:14px; font-family:Verdana, Geneva, sans-serif;">
             JEFE DE TECNOLOGÍAS DE LA INFORMACIÓN
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
                      <p style="font-size:20px; text-align:justify;">Proveer y administrar las tecnologías de información y los sistemas de telecomunicaciones necesarios 
                      para que las unidades administrativas desarrollen sus funciones, y para brindar seguridad y confianza en el manejo 
                      de la información, mediante el fortalecimiento de un gobierno digital y abierto que induzca una mayor participación 
                      de los ciudadanos. </p>
                    </div>
                </div><!-- ./ collapseOne-->
              </div>  <!-- ./ panel box box-primary-->
              <!-- ./ PRIMER COLLAPSE DE OBJETIVOS-->

              <!-- SEGUNDO COLLAPSE DE FUNCIONES -->
              <div class="panel box box-default">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                        
                        <div style="color:#3B0B0B; font-size:24px;"> Oficinas de Tecnologías de la Información</div>
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="box-body">
                    <!-- Link para Soporte Técnico -->
                    <div class="col-md-4" >
                        <div class="info-box" style="background: #E8DDCB;">
                            <span class="info-box-icon" style="background: #413E4A;"><img class="img" src="{!! url('') !!}/img_download/TI/soporte_tecnico.png"/></span>

                            <div class="info-box-content">
                            <a href="{!! url('') !!}/tecnologias/infraestructura" target="_blank" style="color: #000000;"><h5 style="font-size: 18px;"><strong>Infraestructura y <br>Asistencia Técnica</strong></h5></a>
                                
                            </div>
                            
                        </div>
                    </div>

                    <!-- Link para Gobierno Electronico -->
                    <div class="col-md-4">
                      <div class="info-box" style="background: #E8DDCB;">
                          <span class="info-box-icon" style="background: #B38184;"><img class="img" src="{!! url('') !!}/img_download/TI/gobierno_electronico.png"/></span>

                          <div class="info-box-content">
                          <a href="{!! url('') !!}/tecnologias/desarrollo" target="_blank"  style="color: #000000;"><h5 style="font-size: 18px;"><strong>Gobierno Electrónico y <br>Desarrollo de Aplicaciones</strong></h5></a>
                          </div>
                      </div>
                    </div>
                  </div>
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

