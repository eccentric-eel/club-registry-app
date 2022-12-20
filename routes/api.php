<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ExportController;


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

Route::post('/login',       'App\Http\Controllers\RecordController@authenticate');
Route::post('/logout',      'App\Http\Controllers\RecordController@logout');
Route::post('/upload-form', 'App\Http\Controllers\RecordController@store');

Route::middleware('auth:sanctum')->get('/single-record/{recordID}', 'App\Http\Controllers\RecordController@singleRecord');
Route::middleware('auth:sanctum')->post('/delete-record',           'App\Http\Controllers\RecordController@deleteRecord');
Route::middleware('auth:sanctum')->post('/query-records',           'App\Http\Controllers\RecordController@queryRecords');

Route::middleware('auth:sanctum')->post('/export-records', ExportController::class);