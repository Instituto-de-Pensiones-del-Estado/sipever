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
      <div class="widget-user-header bg-white" style="background: url('../img_system/greca.png') center center;">
        <div class="widget-user-image">
          {{ HTML::image('components/admin-lte/dist/img/avatar5.png', 'User Avatar', array('class' => 'img-circle')) }}
        </div>
        <h1 class="widget-user-username">
          L.E.P. YADIRA PETRILLI ZILLI
          <code class="pull-right">
            RECURSOS HUMANOS
          </code>
        </h1>
        <h5 class="widget-user-desc">JEFA DE RECURSOS HUMANOS</h5>
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
                      <p style="font-size:20px; text-align:justify;">Contribuir al éxito de la Institucón y para esto tiene que proveer, mantener y desarrollar un 
                      recursos humanos calificado y motivado para alcanzar los objetivos de la Institución a través de programas eficientes de administración 
                      de recursos humanos, mantener personas que trabajen y den el máximo de sí mismas con una actitud positiva y favorable. </p>
                    </div>
                </div><!-- ./ collapseOne-->
              </div>  <!-- ./ panel box box-primary-->
              <!-- ./ PRIMER COLLAPSE DE OBJETIVOS-->

              <!-- SEGUNDO COLLAPSE DE FUNCIONES -->
              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                      Funciones
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                  <div class="box-body">
                    <ul>
                      <li>Planificar, reclutar y seleccionar el personal necesario para cada área del Instituto en cada momento.</li>
                      <li>Realizar los trámites administrativos que surgen desde que una persona se incorpora al Instituto hasta que deja de formar parte de la misma.</li>
                      <li>Garantizar las correctas relaciones entre el personal que labora en el Instituto.</li>
                      <li>Realizar planes de prevención de riesgos laborales, con el fin de evitar accidentesy prevenir enfermedades laborales(derivadas del puesto de trabajo).</li>
                     
                    </ul>
                  </div>
                </div><!-- ./ collapseTwo-->
              </div> <!-- ./ panel box box-success-->

              <!-- ./ SEGUNDO COLLAPSE DE OBJETIVOS-->


              <!--TERCERO COLLAPSE DE FUNCIONES -->
              <div class="panel box box-danger">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                      Oficinas de Recursos Humanos
                    </a>
                  </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse">
                  <div class="box-body">
                    <!-- Link para Soporte Técnico -->
                    <div class="col-md-4" >
                        <div class="info-box" style="background: #E8DDCB;">
                            <span class="info-box-icon" style="background: #413E4A;"><img class="img" src="{!! url('') !!}/img_download/RH/nomina.png"/></span>

                            <div class="info-box-content">
                              <a href="{!! url('') !!}/recursos_humanos/nomina" target="_blank" style="color: #000000;"><h5 style="font-size: 40px;"><strong>Nómina</strong></h5></a>
                                
                            </div>
                            
                        </div>
                    </div>

                    <!-- Link para Gobierno Electronico -->
                    <div class="col-md-4">
                      <div class="info-box" style="background: #E8DDCB;">
                          <span class="info-box-icon" style="background: #B38184;"><img class="img" src="{!! url('') !!}/img_download/RH/personal.png"/></span>

                          <div class="info-box-content">
                            <a href="{!! url('') !!}/recursos_humanos/personal" target="_blank"  style="color: #000000;"><h5 style="font-size: 40px;"><strong>Personal</strong></h5></a>
                          </div>
                      </div>
                    </div>
                  </div><!-- box body -->
                </div><!-- collapseThree -->  
              </div> <!-- panel box box-danger-->

              <!-- ./ TERCER COLLAPSE DE OBJETIVOS-->

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

