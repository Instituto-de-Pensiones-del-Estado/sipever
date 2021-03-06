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
				<a href="{!! url('catalogos/add_rol') !!}"   class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Rol</a>	
			</div>	 
			 
			 			
			<h3 class="box-title pull-right">Catálogos de Roles</h3>

		</div>
		<div class="box-body">
            <table class="table" id="table"></table> 
 		</div>
 	</div>
</div>


@include('adminlte::layouts.partials.modal_gral')

@endsection

@section('script')

<script type="text/javascript">
		
		$(function (){			
			

			
			var table = $('#table');
			
			

			var formatTableActions = function(value, row, index) {				
				
				btn = '<button class="btn btn-info btn-xs edit"><i class="fa fa-edit"></i>&nbsp;Editar</button>';	
					
				return [btn].join('');
			};

			window.operateEvents = {
				'click .edit': function (e, value, row, index) {
					location.href = routeBase+'/catalogos/roles/edit/'+row.id;						
				},

			}
			
			
				
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
				url: routeBase+'/catalogos/get_roles',
				columns: [{					
					field: 'id',
					title: 'ID.',
				},	{					
					field: 'name',
					title: 'Name',
					filterControl: 'input',	
				},	{					
					field: 'description',
					title: 'Descripcion',
					filterControl: 'input',	
				},  {					
					field: 'guard_name',
					title: 'Tipo',
					filterControl: 'input',	
				},  {
					title: 'Acciones',
					formatter: formatTableActions,
					events: operateEvents
				}]				
			})	// FIN DE LA TABLA 

			
		});// FIN DE LA FUNCION JAVASCRIPT

	</script>
	
@endsection


