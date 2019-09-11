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

Route::get('/','MainController@homePage');
Route::get('/about','MainController@aboutPage');
Route::get('/load','MainController@loadData');
Route::get('/getData','MainController@getData');
Route::get('/projects','ProjectController@index');