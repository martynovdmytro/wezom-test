<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\MakerController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::controller(CarController::class)->group(function ()
{
    Route::get('car/index', 'index');
    Route::post('car/store', 'store');
    Route::get('car/show/{id}', 'show');
    Route::patch('car/update/{id}', 'update');
    Route::delete('car/destroy/{id}', 'destroy');
});

Route::controller( MakerController::class)->group(function ()
{
    Route::get('maker/index', 'index');
    Route::post('maker/store', 'store');
});


