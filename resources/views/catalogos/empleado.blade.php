@extends('adminlte::layouts.landing')

@section('style')
    {!! Html::style('components/bootstrap-table/dist/bootstrap-table.css') !!}
	
@endsection

@section('content')
<div class="col-md-12">

	<div class="box">
		<div class="box-header">
		
			<div class="col-md-4">
				
				<a href="{{ url('/catalogos') }}"  class="btn btn-danger"><span class="fa fa-arrow-circle-left" aria-hidden="true"></span>&nbsp;Regresar</a>
				<a href="{!! url('catalogos/add_empleado') !!}"  class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Empleado</a>	
			</div>	 
					 
			 			
			<h3 class="box-title pull-right">Catálogos de Empleados</h3>

		</div>
		<div class="box-body">
            <table class="table" id="table"></table> 
 		</div>
 	</div>
</div>


@include('adminlte::layouts.partials.modal_gral')

@endsection

@section('script')

	{!! HTML::script('components/select2/dist/js/select2.js') !!}	
	{!! HTML::script('components/select2/dist/js/select2.min.js') !!}

<script type="text/javascript">
		
		$(function (){			
			

			
			var table = $('#table');
			
			
				
			table.bootstrapTable({
				locale: 'es-MX',
				pagination: true,
				exportTypes: ['txt', 'excel', 'doc', 'pdf', 'powerpoint'],
				filterControl:true,
				pageList: [5, 10, 25, 50],
				pageSize: 10,
				search: true,
				searchable: true, 
				smartDisplay: true,
				showColumns: true,
				showExport: true,
				showRefresh: true,
				showFooter:false,
				searchFormatter: true,
				//refreshOptions: true,
				//rowStyle: rowStyle,
				url: routeBase+'/catalogos/get_empleados',
				columns: [{					
					field: 'id',
					title: 'ID.',
				},	{					
					field: 'no_personal',
					title: 'No. de Personal',
					filterControl: 'input',	
				},	{					
					field: 'fecha_ingreso',
					title: 'Fecha de Ingreso',
					filterControl: 'input',	
				},  {
					field: 'apellido_paterno',
					title: 'Apellido Paterno',	
					filterControl: 'input',				
					
				},  {
					field: 'apellido_materno',
					title: 'Apellido Materno',	
					filterControl: 'input',				
					
				},  {
					field: 'nombre',
					title: 'Nombre(s)',	
					filterControl: 'input',				
					
				},  {
					title: 'Acciones',
					//formatter: formatTableActions,
					//events: operateEvents
				}]				
			})	// FIN DE LA TABLA 

			
		});// FIN DE LA FUNCION JAVASCRIPT

	</script>
	
@endsection
