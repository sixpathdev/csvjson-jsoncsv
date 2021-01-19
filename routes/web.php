<?php

use App\Http\Controllers\ConverterController;
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
Route::view('/', 'welcome');
Route::post('/csvjson', [ConverterController::class, 'csvToJson']);
Route::get('/jsoncsv', [ConverterController::class, 'jsonToCsv']);

