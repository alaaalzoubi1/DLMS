<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register',[AccountantController::class,'registerAccountant']);
Route::middleware(['auth:admin','accountant.role','check.subscriber'])->group(function ()
{
    Route::get('orders/filters',[OrderController::class,'OrdersWithFilters']);
    Route::get('logout',[UserController::class,'logout']);
    Route::get('me',[AccountantController::class,'accountantProfile']);
    Route::delete('account',[AccountantController::class,'deleteAccount']);
    Route::post('addPayment',[OrderController::class,'addPayment']);
});
