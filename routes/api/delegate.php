<?php


use App\Http\Controllers\DelegateController;
Route::post('register',[DelegateController::class,'registerDelegate']);
Route::middleware(['auth:admin','delegate.role','check.subscriber'])->group(function ()
{
   Route::get('to-receive',[DelegateController::class,'ordersToReceive']);
   Route::get('to-deliver',[DelegateController::class,'ordersReadyForDelivery']);
   Route::patch('deliver',[DelegateController::class,'deliverOrder']);
   Route::patch('receive',[DelegateController::class,'receiveOrder']);
   Route::get('logout',[\App\Http\Controllers\UserController::class,'logout']);
   Route::get('me',[DelegateController::class,'delegateProfile']);
   Route::delete('account',[DelegateController::class,'deleteAccount']);
});
