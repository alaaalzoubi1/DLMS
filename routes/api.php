<?php

use App\Http\Controllers\Api\RegisterController;

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::post('/register', [RegisterController::class, 'register']);

Route::post('/check_company_code',[SubscriberController::class,'check_company_code']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware(['auth:admin'])->group(function () {
    Route::get('logout', [UserController::class, 'logout']);
});
