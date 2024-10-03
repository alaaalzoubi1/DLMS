<?php


use App\Http\Controllers\Api\RegisterController;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login-admin', [UserController::class, 'loginAdmin']);
Route::post('/register-company', [UserController::class, 'registerCompany']);


Route::middleware(['auth:admin', 'admin.role'])->group(function () {
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get-doctors',[DoctorController::class,'getDoctors']);
    Route::get('get-technicals',[UserController::class,'getTechnical']);
});
