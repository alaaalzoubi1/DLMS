<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register-technical', [UserController::class, 'registerTechnical']);
Route::post('/login-technical', [UserController::class, 'loginTechnical']);
Route::middleware(['auth:admin'])->group(function () {
    Route::get('logout', [UserController::class, 'logout']);
});
