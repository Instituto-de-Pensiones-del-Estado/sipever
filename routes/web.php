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
//Route::get('/directorio', function () { return view('directorio.index'); });

//Route::get('/directorio/get_extension','Directorio\ExtensionController@getExtensiones');
/*Route::group(['middleware' => ['role:admin']], function () {

});*/

Route::post('registro', 'Auth\RegisterController@registro')->name('registro');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function() {
    Route::get('/', function() { return view('admin.index'); })->name('index');
    Route::get('/users', function () { return view('admin.user.index'); });


});

Route::group(['middleware' => ['auth'], 'prefix' => 'almacen', 'as' => 'almacen.'], function() {
    Route::get('/', function() { return view('almacen.index'); })->name('index');
    Route::get('/articulos/page/{no_index}','Almacen\ArticuloController@page')->name('articulos.next_page');
    Route::get('/articulos/actualizar','Almacen\ArticuloController@update')->name('articulos.actualizar');
    Route::get('/articulos/buscar','Almacen\ArticuloController@buscarPorPartida')->name('articulos.buscarPartida');
     Route::get('/articulos/baja/{clave}','Almacen\ArticuloController@destroy')->name('articulos.darBaja');
    Route::resource('periodo','Almacen\PeriodoController');
    Route::resource('articulos', 'Almacen\ArticuloController');

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
	//Módulo de Nosotros
	Route::get('/mision', function () { return view('nosotros.mision'); });
	Route::get('/informacion', function () { return view('nosotros.informacion'); });
	Route::get('/organigrama', function () { return view('nosotros.organigrama'); });
	Route::get('/tecnologias', function () { return view('areas.tecnologias'); });
	Route::get('/tecnologias/infraestructura', function () { return view('areas.soporte'); });
	Route::get('/tecnologias/desarrollo', function () { return view('areas.desarrollo'); });
	Route::get('/descargar_informacion', function () { return view('descargar.informacion'); });


	//Módulo de Extensiones
	Route::get('/extension', function () { return view('extension.index'); });
	Route::get('get_extensiones','Extension\ExtensionController@getExtensiones');

	//Route::get('autocomplete', 'Extension\ExtensionController@search');









