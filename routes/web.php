<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ParameterController;
use App\Http\Controllers\Admin\AutomationRuleController;
use App\Http\Controllers\Admin\ThresholdController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TankController;
use App\Http\Controllers\Admin\TankRequestController as AdminTankRequestController;
use App\Http\Controllers\Admin\FishAnalysisController as AdminFishAnalysisController;
use App\Http\Controllers\Admin\SpeciesController as AdminSpeciesController;
use App\Http\Controllers\TankController as UserTankController;
use App\Http\Controllers\TankRequestController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\TankThresholdController;
use App\Http\Controllers\TankActionController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramLinkController;
use App\Http\Controllers\TankReadingController;
use App\Http\Controllers\TankNotificationController;
use App\Http\Controllers\TankDeviceStateController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\SpeciesSelectionController;
use App\Http\Controllers\CommunityCalculatorController;
use App\Http\Controllers\FishDiseaseController;
use App\Http\Controllers\GoogleNearbyPlaceController;
use App\Http\Controllers\SensorHistoryController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.submit');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::post('dashboard/tank', [UserDashboardController::class, 'selectTank'])->name('dashboard.tank.select');
    Route::get('sensor-history', [SensorHistoryController::class, 'index'])->name('sensor-history.index');
    Route::get('species-selection', [SpeciesSelectionController::class, 'index'])->name('species.index');
    Route::post('species-selection', [SpeciesSelectionController::class, 'update'])->name('species.update');
    Route::get('community-calculator', [CommunityCalculatorController::class, 'index'])->name('community.index');
    Route::post('community-calculator/calculate', [CommunityCalculatorController::class, 'calculate'])->name('community.calculate');
    Route::post('community-calculator/apply', [CommunityCalculatorController::class, 'apply'])->name('community.apply');
    Route::get('image-analysis', [FishDiseaseController::class, 'index'])->name('image-analysis.index');
    Route::get('ai-analysis/treatment-guide', [FishDiseaseController::class, 'treatmentGuide'])
        ->name('image-analysis.treatment-guide');
    Route::get('ai-analysis/nearby-aquarium-shops', [GoogleNearbyPlaceController::class, 'index'])
        ->name('image-analysis.nearby-shops');
    Route::post('api/v1/vision/check-fish', [FishDiseaseController::class, 'checkDisease'])
        ->name('image-analysis.check');
    Route::get('tank-requests/create', [TankRequestController::class, 'create'])->name('tank-requests.create');
    Route::post('tank-requests', [TankRequestController::class, 'store'])->name('tank-requests.store');
    Route::get('tank-requests/{tankRequest}', [TankRequestController::class, 'show'])->name('tank-requests.show');
    Route::resource('tanks', UserTankController::class)->only([
        'index', 'show', 'edit', 'update', 'destroy',
    ]);
    Route::get('tanks/{tank}/dashboard', [UserDashboardController::class, 'show'])
        ->name('tanks.dashboard');
    Route::post('tanks/{tank}/actions', [TankActionController::class, 'store'])
        ->name('tanks.actions.store');
    Route::post('tanks/{tank}/readings', [TankReadingController::class, 'store'])
        ->name('tanks.readings.store');
    Route::post('tanks/{tank}/mode', [UserTankController::class, 'updateMode'])
        ->name('tanks.mode.update');
    Route::get('tanks/{tank}/devices', [TankDeviceStateController::class, 'index'])
        ->name('tanks.devices.index');
    Route::post('tanks/{tank}/devices', [TankDeviceStateController::class, 'update'])
        ->name('tanks.devices.update');
    Route::post('tanks/{tank}/dose', [TankDeviceStateController::class, 'dose'])
        ->name('tanks.devices.dose');
    Route::post('tanks/{tank}/notify', [TankNotificationController::class, 'send'])
        ->name('tanks.notify');
    Route::get('telegram-integration', [TelegramLinkController::class, 'index'])
        ->name('telegram.index');
    Route::post('telegram/link', [TelegramLinkController::class, 'generate'])
        ->name('telegram.link');
    Route::get('tanks/{tank}/thresholds', [TankThresholdController::class, 'index'])->name('tanks.thresholds.index');
    Route::post('tanks/{tank}/thresholds', [TankThresholdController::class, 'store'])->name('tanks.thresholds.store');
    Route::get('tanks/{tank}/thresholds/{threshold}/edit', [TankThresholdController::class, 'edit'])
        ->name('tanks.thresholds.edit');
    Route::put('tanks/{tank}/thresholds/{threshold}', [TankThresholdController::class, 'update'])
        ->name('tanks.thresholds.update');
    Route::delete('tanks/{tank}/thresholds/{threshold}', [TankThresholdController::class, 'destroy'])
        ->name('tanks.thresholds.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('parameters', ParameterController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('automation-rules', AutomationRuleController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('thresholds', ThresholdController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('species', AdminSpeciesController::class)->names('admin.species');
    Route::resource('users', UserController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('tank-requests', [AdminTankRequestController::class, 'index'])->name('admin.tank-requests.index');
    Route::get('tank-requests/{tankRequest}', [AdminTankRequestController::class, 'show'])->name('admin.tank-requests.show');
    Route::put('tank-requests/{tankRequest}', [AdminTankRequestController::class, 'update'])->name('admin.tank-requests.update');
    Route::get('tanks', [TankController::class, 'index'])->name('admin.tanks.index');
    Route::get('tanks/{tank}', [TankController::class, 'show'])->name('admin.tanks.show');
    Route::get('fish-analyses', [AdminFishAnalysisController::class, 'index'])->name('admin.fish-analyses.index');
});
