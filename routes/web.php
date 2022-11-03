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

Route::get('/',      function() { return view('index'); });
Route::get('/admin', function() { return view('index'); });
Route::get('/admin/records', 'App\Http\Controllers\RecordsController@index');
Route::post('/admin/records/query-records', 'App\Http\Controllers\RecordsController@queryRecords');
Route::post('/admin/records/export-records', 'App\Http\Controllers\RecordsController@exportRecords');

Route::get('/{any}',       function() { return redirect('/'); });
Route::get('/admin/{any}', function() { return redirect('/'); });

Route::post('/upload-form', 'App\Http\Controllers\RecordsController@store');