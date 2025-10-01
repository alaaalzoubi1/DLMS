<?php


use App\Http\Controllers\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin','super_admin.role'])->group(function () {
    Route::prefix('plans')->group(function (){
        Route::get('/', [SubscriptionPlanController::class, 'index']);
        Route::post('/', [SubscriptionPlanController::class, 'store']);
        Route::get('/{id}', [SubscriptionPlanController::class, 'show']);
        Route::delete('/{id}', [SubscriptionPlanController::class, 'destroy']);
    });
});
