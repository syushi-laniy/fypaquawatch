<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\IoTController;
use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\TelegramController;

Route::post('/iot/readings', [IoTController::class, 'storeReadings']);
Route::get('/iot/commands', [IoTController::class, 'commands']);
Route::post('/iot/commands/{command}/acknowledge', [IoTController::class, 'acknowledgeCommand']);
Route::post('/iot/alert', [IoTController::class, 'alert']);
Route::post('/sensor-readings', [SensorReadingController::class, 'store']);
Route::get('/tanks/{tank}/latest-readings', [SensorReadingController::class, 'latestReadings']);
Route::get('/tanks/{tank}/latest', [SensorReadingController::class, 'latest']);
Route::post('/telegram/webhook', [TelegramController::class, 'handle']);


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
