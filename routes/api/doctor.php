<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClinicHeaderController;
use App\Http\Controllers\ClinicSubscriberController;
use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\DoctorController;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderFileController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ToothColorController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [DoctorController::class, 'doctorRegister']);
Route::post('/login', [DoctorController::class, 'doctorLogin']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/clinic-subscribers', [ClinicSubscriberController::class, 'index']);
    Route::post('/clinic-subscribers', [ClinicSubscriberController::class, 'store']);
    Route::delete('/clinic-subscribers/{subscriber}', [ClinicSubscriberController::class, 'destroy']);
    Route::get('/subscribers/{id}', [SubscriberController::class, 'show']);
    Route::get('types/{subscriber_id}', [\App\Http\Controllers\TypeController::class, 'doctorListTypes']);
    Route::get('tooth-colors/{subscriber_id}', [ToothColorController::class, 'doctorShow']);
    Route::get('specializations/{subscriber_id}',[\App\Http\Controllers\SpecializationSubscriberController::class,'doctorShow']);
    Route::post('create-order',[OrderController::class,'doctorCreateOrder']);
    Route::get('orders',[OrderController::class,'doctorOrders']);
    Route::get('patients',[DoctorController::class,'doctorPatients']);
    Route::get('/logout', [DoctorController::class, 'logout']);
    Route::get('profile',[DoctorController::class,'doctorProfile']);
    Route::get('categories/{subscriber_id}',[CategoryController::class,'subscriberCategories']);
    Route::get('products',[\App\Http\Controllers\ProductController::class,'categoryProducts']);
    Route::patch('profile',[DoctorController::class,'updateProfile']);
    Route::delete('profile',[DoctorController::class,'deleteAccount']);
    Route::get('order-details',[OrderController::class,'orderDetails']);
    Route::post('invoice-header',[ClinicHeaderController::class,'store']);
    Route::post('invoice-header/update',[ClinicHeaderController::class,'update']);
    Route::get('invoice-header',[ClinicHeaderController::class,'getHeader']);
    Route::delete('invoice-header',[ClinicHeaderController::class,'delete']);

    Route::prefix('contact-info')->group(function () {
        Route::get('/', [ContactInfoController::class, 'index']);
    });
    Route::prefix('order-files')->middleware('auth')->group(function () {
        Route::post('/upload', [OrderFileController::class, 'createUpload']);
        Route::post('/{id}/uploaded', [OrderFileController::class, 'markUploaded']);
    });


});
