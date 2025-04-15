<?php


use App\Http\Controllers\Api\RegisterController;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\ToothColorController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login-admin', [UserController::class, 'loginAdmin']);
Route::post('/register-company', [UserController::class, 'registerCompany']);


Route::middleware(['auth:admin', 'admin.role'])->group(function () {
    Route::get('admin-info', [UserController::class,'adminInfo']);
    Route::post('add-category',[CategoryController::class,'store']);
    Route::get('show-categories', [CategoryController::class,'show']);
    Route::get('delete-category/{id}',[CategoryController::class,'delete']);
    Route::post('add-product',[ProductController::class,'store']);
    Route::get('showProductsByCategory/{category_id}',[ProductController::class,'showByCategory']);
    Route::post('edit-category',[CategoryController::class,'update']);
    Route::get('delete-product/{id}',[ProductController::class,'delete']);
    Route::post('update-price',[ProductController::class,'updatePrice']);
    Route::post('add-toothColor',[ToothColorController::class,'add']);
    Route::get('delete-toothColor/{id}',[ToothColorController::class,'delete']);
    Route::get('show-toothColor',[ToothColorController::class,'show']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get-doctors',[DoctorController::class,'getDoctors']);
    Route::get('get-technicals',[UserController::class,'getTechnical']);
    Route::post('add-specialization',[SpecializationController::class,'addSpecialization']);
    Route::post('clinics', [ClinicController::class, 'store']);
    Route::get('clinics', [ClinicController::class, 'show']);
    Route::put('clinics/{id}', [ClinicController::class, 'edit']);
    Route::delete('/clinics/{id}', [ClinicController::class, 'destroy']);
    Route::post('doctors', [DoctorController::class, 'store']);
    Route::get('doctors/{id}', [DoctorController::class, 'show']);
    Route::get('doctorsByClinic/{id}', [DoctorController::class, 'doctorsByClinic']);
    Route::delete('doctors/{id}', [DoctorController::class, 'destroy']);
    Route::post('specializations', [SpecializationController::class, 'store']);
    Route::get('specializations', [SpecializationController::class, 'getSpecializationsBySubscriber']);
    Route::delete('specializations/{id}',[SpecializationController::class,'delete']);
    Route::post('add-special-price', [ClinicController::class, 'addSpecialPrice']);
    Route::delete('delete-special-price', [ClinicController::class, 'deleteSpecialPrice']);
    Route::patch('availability/{id}', [UserController::class, 'setAvailability']);
    Route::get('clinics_with_special_price/{$subscriberId}',[ClinicController::class,'clinics_with_special_price']);
    Route::get('get_clinics_with_the_special_price/{id}',[ProductController::class,'get_clinics_with_the_special_price']);
    Route::post('orders',[OrderController::class,'createOrder']);
    Route::put('orders/{id}',[OrderController::class,'updateOrder']);
    Route::get('orders/{type}',[OrderController::class,'listInvoices']);
    Route::get('listOrdersByStatus/{status}',[OrderController::class,'listOrdersByStatus']);
    Route::post('doctor-orders',[OrderController::class,'listDoctorInvoices']);
    Route::post('from-to-orders',[OrderController::class,'listFromToInvoices']);
    Route::post('/types',[TypeController::class,'createType']);
    Route::get('/types', [TypeController::class, 'listTypes']);
    Route::put('/types/{id}', [TypeController::class, 'updateType']);
});


