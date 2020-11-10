<?php

use Illuminate\Support\Facades\Route;

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
Route::get('showuser', 'UserController@index')->name('showuser');
Route::get('edituser/{id}', 'UserController@edit')->name('edituser');
Route::patch('edituser/{id}', 'UserController@update')->name('edituser');

