<?php

use Illuminate\Support\Facades\Route;
use App\Modelo;
use App\Componente;
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
    
    return view('/auth/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('gestionop', 'gestionopController@index')->name('gestionop');
Route::get('gestionop1', 'gestionopController@create')->name('gestionop1');
Route::post('gestionop1', 'gestionopController@store')->name('gestionop1');
Route::get('gestionopedit/{id}', 'gestionopController@edit')->name('gestionopedit');
Route::patch('gestionopedit/{id}', 'gestionopController@update')->name('gestionopedit');
Route::delete('gestionopdes/{id}', 'gestionopController@destroy')->name('gestionopdes');
Route::post('gestionop/fetch', 'gestionopController@fetch')->name('empresa.fetch');


Route::get('showuser', 'UserController@index')->name('showuser');
Route::get('edituser/{id}', 'UserController@edit')->name('edituser');
Route::patch('edituser/{id}', 'UserController@update')->name('edituser');
Route::get('showuser1/{id}', 'UserController@show')->name('showuser1');



Route::get('componenteop', 'ComponenteController@index')->name('componenteop');
Route::get('componenteop1', 'ComponenteController@create')->name('componenteop1');
Route::post('componenteop1', 'ComponenteController@store')->name('componenteop1');
Route::get('componenteopedit/{id}', 'ComponenteController@edit')->name('componenteopedit');
Route::patch('componenteopedit/{id}', 'ComponenteController@update')->name('componenteopedit');
Route::delete('componenteopdes/{id}', 'ComponenteController@destroy')->name('componenteopdes');
Route::get('componenteopshow/{id}', 'ComponenteController@show')->name('componenteopshow');

Route::get('modelosop', 'ModeloController@index')->name('modelosop');
Route::get('modelosop1', 'ModeloController@create')->name('modelosop1');
Route::post('modelosop1', 'ModeloController@store')->name('modelosop1');
Route::get('modelosopedit/{id}', 'ModeloController@edit')->name('modelosopedit');
Route::patch('modelosopedit/{id}', 'ModeloController@update')->name('modelosopedit');
Route::delete('modelosopdes/{id}', 'ModeloController@destroy')->name('modelosopdes');
Route::get('modelosopshow/{id}', 'ModeloController@show')->name('modelosopshow');

Route::get('empresaop', 'EmpresaController@index')->name('empresaop');
Route::get('empresaop1', 'EmpresaController@create')->name('empresaop1');
Route::post('empresaop1', 'EmpresaController@store')->name('empresaop1');
Route::get('empresaopedit/{id}', 'EmpresaController@edit')->name('empresaopedit');
Route::patch('empresaopedit/{id}', 'EmpresaController@update')->name('empresaopedit');
Route::delete('empresaopdes/{id}', 'EmpresaController@destroy')->name('empresaopdes');

Route::get('asignarop', 'asignaropController@index')->name('asignarop');
Route::get('asignarop1', 'asignaropController@create')->name('asignarop1');
Route::post('asignarop1', 'asignaropController@store')->name('asignarop1');
Route::get('asignaropedit/{id}', 'asignaropController@edit')->name('asignaropedit');
Route::patch('asignaropedit/{id}', 'asignaropController@update')->name('asignaropedit');
Route::delete('asignaropdes/{id}', 'asignaropController@destroy')->name('asignaropdes');

Route::get('historialop', 'HistorialController@index')->name('historialop');
Route::get('historialopshow/{id}', 'HistorialController@show')->name('historialopshow');

Route::get('/sendemail', 'SendEmailController@index')->name('/sendemail');
Route::post('/sendemail/send', 'SendEmailController@send')->name('/sendemail1');

Route::get('reporteop', 'ReporteController@index')->name('reporteop');
Route::delete('reporteopdes/{id}', 'ReporteController@destroy')->name('reporteopdes');
Route::get('reporteopedit/{id}', 'ReporteController@edit')->name('reporteopedit');
Route::patch('reporteopedit/{id}', 'ReporteController@update')->name('reporteopedit');

Route::get('documentosop', 'DocumentoController@index')->name('documentosop');
Route::get('documentosop1', 'DocumentoController@create')->name('documentosop1');
Route::post('documentosop1', 'DocumentoController@store')->name('documentosop1');
Route::get('documentosopshow/{id}', 'DocumentoController@show')->name('documentosopshow');


Route::get('documentosopdownload/{file}','DocumentoController@download')->name('documentosopdownload');

Route::get('ayudaop', 'AyudaController@index')->name('ayudaop');


