<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login',  'App\Http\Controllers\RecordsController@authenticate');
Route::post('/logout', 'App\Http\Controllers\RecordsController@logout');

Route::middleware('auth:sanctum')->post('/upload-form',    'App\Http\Controllers\RecordsController@store');
Route::middleware('auth:sanctum')->post('/query-records',  'App\Http\Controllers\RecordsController@queryRecords');
Route::middleware('auth:sanctum')->post('/export-records', 'App\Http\Controllers\RecordsController@exportRecords');