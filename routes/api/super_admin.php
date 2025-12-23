<?php


use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:admin','super_admin.role'])->group(function () {
    Route::prefix('plans')->group(function (){
        Route::get('/', [SubscriptionPlanController::class, 'index']);
        Route::post('/', [SubscriptionPlanController::class, 'store']);
        Route::get('/{id}', [SubscriptionPlanController::class, 'show']);
        Route::delete('/{id}', [SubscriptionPlanController::class, 'destroy']);
    });

    Route::prefix('contact-info')->group(function () {
        Route::get('/', [ContactInfoController::class, 'index']);
        Route::post('/', [ContactInfoController::class, 'store']);
        Route::patch('/{id}', [ContactInfoController::class, 'update']);
        Route::delete('/{id}', [ContactInfoController::class, 'destroy']);
    });
    Route::prefix('subscribers')->group(function (){
        Route::get('/',[SubscriberController::class,'index']);
    });
});
