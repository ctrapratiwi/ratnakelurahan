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

Route::get('/','KabupatenController@index');

Route::resource('/pasien','PasienController');
Route::resource('/kabupaten','KabupatenController');
Route::get('/getKecamatan','PasienController@getKecamatan');
Route::get('/getKelurahan','PasienController@getKelurahan');
Route::get('/create-pallete','KabupatenController@createPallette');
Route::get('/getData','KabupatenController@getData');
Route::get('/getPositif','KabupatenController@getPositif');
Route::post('/search','KabupatenController@search');
