<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CountryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('zones/create', [ZoneController::class, 'zoneCreate']);
Route::get('/zones/{id}', [ZoneController::class, 'getZoneById']);
Route::delete('/zones/{id}', [ZoneController::class, 'deleteById']);
Route::post('/zones/changeStatus', [ZoneController::class, 'changeZoneStatus']);
Route::post('/zones/getZoneStatusCounter', [ZoneController::class, 'getZoneStatusCounter']);



Route::post('country/create', [CountryController::class, 'countryCreate']);
Route::get('/country/{id}', [CountryController::class, 'getCountryById']);
Route::delete('/country/{id}', [CountryController::class, 'deleteById']);
Route::post('/country/changeStatus', [CountryController::class, 'changeCountryStatus']);
Route::post('/country/getCountryStatusCounter', [CountryController::class, 'getCountryStatusCounter']);


