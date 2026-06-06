<?php


use App\Http\Controllers\DelegateController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('register',[DelegateController::class,'registerDelegate']);
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/password/forgot', [ForgotPasswordController::class, 'requestResetCode']);
Route::middleware(['auth:admin','delegate.role','check.subscriber'])->group(function ()
{
   Route::get('to-receive',[DelegateController::class,'ordersToReceive']);
   Route::get('to-deliver',[DelegateController::class,'ordersReadyForDelivery']);
   Route::get('orders/filters',[\App\Http\Controllers\OrderController::class,'OrdersWithFilters']);
   Route::patch('deliver',[DelegateController::class,'deliverOrder']);
   Route::patch('receive',[DelegateController::class,'receiveOrder']);
   Route::get('logout',[\App\Http\Controllers\UserController::class,'logout']);
   Route::get('me',[DelegateController::class,'delegateProfile']);
   Route::delete('account',[DelegateController::class,'deleteAccount']);
});
