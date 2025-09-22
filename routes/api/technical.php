<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SpecializationSubscriberController;
use App\Http\Controllers\SpecializationUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register-technical', [UserController::class, 'registerTechnical']);
Route::post('/login-technical', [UserController::class, 'loginTechnical']);
Route::middleware(['auth:admin'])->group(function () {
    Route::post('add-specialization', [SpecializationUserController::class, 'addUserSpecialization']);
    Route::get('subscriber-specializations', [SpecializationSubscriberController::class, 'getSubscriberSpecializations']);
    Route::get('user-specializations', [SpecializationUserController::class, 'getUserSpecializations']);
    Route::delete('user-specializations/{specializationId}', [SpecializationUserController::class, 'deleteUserSpecialization']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::post('updateOrderProductSpecializationUser',[OrderController::class,'updateOrderProductSpecializationUser']);
    Route::get('products', [ProductController::class, 'getProductsWithSpecializations']);
    Route::get('finishing-order_product/{order_product_id}',[ProductController::class , 'finishOrderProduct']);
    Route::get('availability', [UserController::class, 'getAvailability']);
    Route::patch('availability', [UserController::class, 'toggleAvailability']);

});
