<?php


use App\Http\Controllers\Api\RegisterController;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\ToothColorController;
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
    Route::get('delete-product/{id}',[ProductController::class,'delete']);
    Route::post('update-price',[ProductController::class,'updatePrice']);
    Route::post('add-toothColor',[ToothColorController::class,'add']);
    Route::get('delete-toothColor/{id}',[ToothColorController::class,'delete']);
    Route::get('show-toothColor',[ToothColorController::class,'show']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get-doctors',[DoctorController::class,'getDoctors']);
    Route::get('get-technicals',[UserController::class,'getTechnical']);
    Route::post('add-specialization',[SpecializationController::class,'addSpecialization']);
});
