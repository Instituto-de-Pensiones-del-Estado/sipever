<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
});

Route::get('get_extensiones','Directorio\ExtensionController@getExtensiones');



/*Route::group(['middleware' => ['role:admin']], function () {
    
});*/

Route::post('registro', 'Auth\RegisterController@registro')->name('registro');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('/', function() { return view('admin.index'); })->name('index');
    Route::get('/users', function () { return view('admin.user.index'); });
 
    
});

Route::group(['middleware' => ['auth'], 'prefix' => 'almacen', 'as' => 'almacen.'], function() {
    Route::get('/', function() { return view('almacen.index'); })->name('index');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'compras', 'as' => 'compras.'], function() {
    Route::get('/', function() { return view('compras.index'); })->name('index');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'expediente', 'as' => 'expediente.'], function(){
	
	// MOSTRAR LA PAGINA PRINCIPAL DEL EXPEDIENTE ELECTRONICO
	Route::get('/', function () { return view('expediente.index'); })->name('index');

	// DIRECCIONA PARA AGREGAR FORMULARIO DEL ACTIVO O PENSIONADO
	Route::get('add', 'Expediente\ExpedienteController@create');
	
	// INSERTAR VALORES EN EL FORMULARIO
	Route::post('/actpen', 'Expediente\ExpedienteController@store');

	// MOSTRAR LA INFORMACION DE LOS ACTIVOS Y PENSIONADOS EN LA TABLA DE LA PAGINA PRINCIPAL
	Route::get('get_expediente','Expediente\ExpedienteController@getExpedientes');

	// MOSTRAR EL DETALLE DEL ACTIVO O PENSIONADO DESDE LA TABLA DE LA PAGINA PRINCIPAL
	Route::get('/{id}', 'Expediente\ExpedienteController@show');

	//EDITAR REGISTRO
	Route::get('/edit/{id}','Expediente\ExpedienteController@edit'); //LLeva al formulario
	Route::put('/update/{id}', 'Expediente\ExpedienteController@update'); //Edita la información

	//Route::get('/organismos/{idOrganismo}', 'Expediente\ExpedienteController@getDependencias');

	Route::get('{id}/plaza','Expediente\ExpedienteController@plaza'); //LLeva al formulario

	
});

//$router->get('import', 'ImportController@import');
 




