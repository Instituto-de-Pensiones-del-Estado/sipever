@extends('adminlte::layouts.landing')

@section('style')
	
{!! Html::style('components/bootstrap-table/dist/bootstrap-table.css') !!}

  <style>
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
      <div class="widget-user-header bg-white" style="background: url('../img_system/greca.png') center center;">
        <div class="widget-user-image">
          {{ HTML::image('components/admin-lte/dist/img/avatar5.png', 'User Avatar', array('class' => 'img-circle')) }}
        </div>
        <h1 class="widget-user-username">
          C. JAQUELINE GONZÁLEZ IBAÑEZ 
          <code class="pull-right">
            RECURSOS HUMANOS
          </code>
        </h1>
        <h5 class="widget-user-desc">JEFA DE OFICINA DE PERSONAL</h5>
      </div><!-- /. widget-user-header bg-yellow -->

      <!--COLLAPSE DE FORMATOS DE PERMISOS -->     
      <div class="box box-solid">
          <div class="box-body">

            <div class="box-group" id="accordion">

              <!-- PRIMER COLLAPSE DE OBJETIVOS-->
              <div class="panel box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Objetivo
                      </a>
                    </h4>
                </div>
                
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="box-body">
                      <p style="font-size:20px; text-align:justify;"> Dirigir el movimiento de personal, escalafón, remuneraciones y pensiones del Instituto. </p>
                    </div>
                </div><!-- ./ collapseOne-->
              </div>  <!-- ./ panel box box-primary-->
              <!-- ./ PRIMER COLLAPSE DE OBJETIVOS-->

              <!-- SEGUNDO COLLAPSE DE FUNCIONES -->
              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                      Descargar Formatos
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="box-body">
                    
                    




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



