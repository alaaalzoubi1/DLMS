<?php

use App\Http\Controllers\ClinicSubscriberController;
use App\Http\Controllers\DoctorController;

use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [DoctorController::class, 'doctorRegister']);
Route::post('/login', [DoctorController::class, 'doctorLogin']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/clinic-subscribers', [ClinicSubscriberController::class, 'index']);
    Route::post('/clinic-subscribers', [ClinicSubscriberController::class, 'store']);
    Route::delete('/clinic-subscribers/{subscriber}', [ClinicSubscriberController::class, 'destroy']);
    Route::get('/subscribers/{id}', [SubscriberController::class, 'show']);
    Route::get('/logout', [DoctorController::class, 'logout']);
});
