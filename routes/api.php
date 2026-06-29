<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SensorReadingController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\StripeWebhookController;

Route::post('/sensor-readings', [SensorReadingController::class, 'store']);
Route::get('/tanks/{tank}/latest', [SensorReadingController::class, 'latest']);
Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);


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
