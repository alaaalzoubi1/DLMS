<?php

use App\Http\Controllers\DoctorController;

use Illuminate\Support\Facades\Route;

Route::post('/register-doctor', [DoctorController::class, 'registerDoctor']);
Route::post('/login-doctor', [DoctorController::class, 'loginDoctor']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', [DoctorController::class, 'logout']);
});
