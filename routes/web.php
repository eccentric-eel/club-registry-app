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

Route::get('/',              'App\Http\Controllers\RecordController@index');
Route::get('/admin',         'App\Http\Controllers\RecordController@index');
Route::get('/admin/records', 'App\Http\Controllers\RecordController@records');

Route::get('/{any}',       function() { return redirect('/'); });
Route::get('/admin/{any}', function() { return redirect('/'); });