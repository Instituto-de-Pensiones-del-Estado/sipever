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
            L.I. MIGUEL ÁNGEL ROJAS
            <br>
            
            <div style="font-size:14px; font-family:Verdana, Geneva, sans-serif;">
            JEFE DE OFICINA DE INFRAESTRUCTURA Y ASISTENCIA TÉCNICA
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
                      <p style="font-size:20px; text-align:justify;">Mantener en el tiempo la continuidad operativa de la plataforma tecnológica de los usuarios, consiguiendo con esto optimizar la productividad de ellos, mediante el mantenimiento preventivo y correctivo de la plataforma computacional, todo esto dentro de tiempos de solución definidos.</p>
                    </div>
                </div><!-- ./ collapseOne-->
              </div>  <!-- ./ panel box box-primary-->
              <!-- ./ PRIMER COLLAPSE DE OBJETIVOS-->

             
             

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

