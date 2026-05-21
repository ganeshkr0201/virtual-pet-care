<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetApiController;
use App\Http\Controllers\Api\ReminderApiController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Virtual Pet Care
|--------------------------------------------------------------------------
| All routes are versioned under /api/v1
| Authentication via Laravel Sanctum tokens
*/

Route::prefix('v1')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // ── Authenticated ────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me',      [AuthController::class, 'me']);

        // Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'index']);

        // Pets
        Route::apiResource('pets', PetApiController::class)->names([
            'index'   => 'api.pets.index',
            'store'   => 'api.pets.store',
            'show'    => 'api.pets.show',
            'update'  => 'api.pets.update',
            'destroy' => 'api.pets.destroy',
        ]);

        // Reminders
        Route::apiResource('reminders', ReminderApiController::class)->names([
            'index'   => 'api.reminders.index',
            'store'   => 'api.reminders.store',
            'show'    => 'api.reminders.show',
            'update'  => 'api.reminders.update',
            'destroy' => 'api.reminders.destroy',
        ]);
        Route::post('/reminders/{reminder}/complete', [ReminderApiController::class, 'complete'])->name('api.reminders.complete');
        Route::post('/reminders/{reminder}/snooze',   [ReminderApiController::class, 'snooze'])->name('api.reminders.snooze');

        // Notifications
        Route::get('/notifications',          fn(\Illuminate\Http\Request $r) => response()->json(
            $r->user()->notifications()->paginate(20)
        ));
        Route::post('/notifications/read-all', fn(\Illuminate\Http\Request $r) => response()->json(
            tap($r->user()->unreadNotifications->markAsRead(), fn() => null) ?? ['success' => true]
        ));
    });
});
