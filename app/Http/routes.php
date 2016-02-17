<?php

/**************************ENRUTADO LISTO ************************/
Route::group(['prefix' => 'api'], function(){
    //Route::resource('facultad', 'FacultadController');
    Route::get('facultad/{id_usuario}', 'FacultadController@index');
    Route::get('facultad/{id_usuario}/{id_facultad}', 'FacultadController@show');
    Route::post('facultad/{id_usuario}', 'FacultadController@store');
    Route::put('facultad/{id_usuario}', 'FacultadController@update');
    Route::put('facultad/eliminar/{id_usuario}/{id_facultad}', 'FacultadController@cambiar_estado');
});

Route::group(['prefix' => 'api'], function(){
    //Route::resource('programa', 'ProgramaController');
    Route::get('programa', 'ProgramaController@index');
    Route::get('programa/{id_usuario}', 'ProgramaController@index');
    Route::get('programa/{id_usuario}/{id_facultad}', 'ProgramaController@show');
    Route::get('programa/id_programa/{id_programa}', 'ProgramaController@get_programa');
    Route::post('programa/{id_usuario}', 'ProgramaController@store');
    Route::put('programa/{id_usuario}', 'ProgramaController@update');
    Route::put('programa/eliminar/{id:usuario}/{id_programa}', 'ProgramaController@cambiar_estado');
});

Route::group(['prefix' => 'api'], function(){
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('authenticate', 'AuthenticateController@index');
    Route::get('getAuthenticatedUser', 'AuthenticateController@getAuthenticatedUser');
});

Route::group(['prefix' => 'api'], function(){
    //Route::resource('universidad', 'UniversidadController');
    Route::get('universidad', 'UniversidadController@index');
    Route::get('universidad/{id_usuario}/{id_universidad}', 'UniversidadController@show');
    Route::post('universidad/{id_usuario}', 'UniversidadController@store');
    Route::put('universidad/{id_usuario}', 'UniversidadController@update');
    Route::put('universidad/eliminar/{id_usuario}/{id_universidad}', 'UniversidadController@cambiar_estado');
});

Route::group(['prefix' => 'api'], function(){
    //Route::resource('programa-universidad', 'programa_universidadController');
    Route::get('programa-universidad', 'programa_universidadController@index');
    Route::get('programa-universidad/{id_usuario}/{id_programa}', 'programa_universidadController@show');
    Route::get('programa-universidad/{id_usuario}/id_programa', 'programa_universidadController@get_universidad');
    Route::post('programa-universidad/{id_usuario}', 'programa_universidadController@store');
});

Route::group(['prefix' => 'api'], function()
{
    Route::get('competencia/{id_usuario}', 'CompetenciaController@index');
    Route::get('competencia/{id_usuario}/{id_competencia}', 'CompetenciaController@show');
    Route::get('competencia/id_usuario/{id_usuario}/categoria/{categoria}', 'CompetenciaController@get_categoria');
    Route::post('competencia/{id_usuario}', 'CompetenciaController@store');
    Route::put('competencia/{id_usuario}', 'CompetenciaController@update');
    Route::put('competencia/eliminar/{id_usuario}/{id_competencia}', 'CompetenciaController@cambiar_estado');
});

Route::group(['prefix' => 'api/competencia-programa'], function(){
    Route::get('{id_usuario}/{id_programa}', 'competencia_programaController@get_competencias');
    Route::post('{id_usuario}', 'competencia_programaController@store');
    Route::put('{id_usuario}', 'competencia_programaController@update');
});

Route::group(['prefix' => 'api/encabezado'], function(){
    Route::get('{id_usuario}', 'EncabezadoController@index');
    Route::get('{id_usuario}/multimedia/{multimedia}', 'EncabezadoController@filtro_multimedia');
    Route::get('{id_usuario}/{id_encabezado}', 'EncabezadoController@show');
    Route::post('{id_usuario}', 'EncabezadoController@store');
    Route::post('/actualizar/{id_usuario}', 'EncabezadoController@update');
});

Route::group(['prefix' => 'api/pregunta'], function(){
    Route::get('{id_usuario}', 'PreguntaController@index');
    Route::get('{id_usuario}/competencia/{id_competencia}', 'PreguntaController@filtro_competencia');
    Route::get('{id_usuario}/{id_pregunta}', 'PreguntaController@show');
    Route::post('{id_usuario}', 'PreguntaController@store');
    Route::post('actualizar/{id_usuario}', 'PreguntaController@update');
});

Route::group(['prefix' => 'api/opcion'], function(){
    Route::get('{id_usuario}/{id_opcion}', 'OpcionController@show');
});
/******************************************************************/

Route::group(['prefix' => 'api'], function(){
    Route::resource('usuarios', 'UsuariosController');
    Route::get('usuarios/GetPruebaToken/{id_usuario}', 'UsuariosController@GetPruebaToken');
});