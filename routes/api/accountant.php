<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LabHeaderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderProductHistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Zatca\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::post('register',[AccountantController::class,'registerAccountant']);
Route::middleware(['auth:admin','accountant.role','check.subscriber'])->group(function ()
{
    Route::get('home-page',[SubscriberController::class,'dashboardStats']);
    Route::get('logout',[UserController::class,'logout']);
    Route::get('me',[AccountantController::class,'accountantProfile']);
    Route::delete('account',[AccountantController::class,'deleteAccount']);
    Route::post('addPayment',[OrderController::class,'addPayment']);
    Route::prefix('orders')->group(function (){

        Route::prefix('order-products')->group(function () {

            Route::prefix('history')->group(function (){
                Route::get('/', [OrderProductHistoryController::class, 'index']);
                Route::get('/{orderProductId}', [OrderProductHistoryController::class, 'orderProductHistory']);
            });

            Route::post('',[OrderProductController::class,'store']);
            Route::put('/{id}',[OrderProductController::class,'update']);
            Route::delete('/{id}',[OrderProductController::class,'destroy']);

        });

        Route::prefix('discount')->group(function (){
            Route::post('',[OrderController::class,'applyDiscount']);
            Route::put('',[OrderController::class,'updateDiscount']);
            Route::delete('/{discount_id}',[OrderController::class,'removeDiscount']);
        });

        Route::post('/',[OrderController::class,'createOrder']);
        Route::get('/filters',[OrderController::class,'OrdersWithFilters']);
        Route::put('/{id}',[OrderController::class,'updateOrder']);
        Route::post('/invoice/bulk',[InvoiceController::class,'invoiceBulk'])->middleware('check.zatca');
        Route::post('/credit-note',[InvoiceController::class,'submitCreditNote'])->middleware('check.zatca');
        Route::get('/{id}/details',[OrderController::class,'orderDetails']);
    });

    Route::prefix('reports')->group(function () {
        Route::get('/revenue', [ReportController::class,'revenue']);
        Route::get('/doctors-due', [ReportController::class,'doctorsDue']);
        Route::get('/clinic-due',[ReportController::class,'clinicDoctorsDue']);
        Route::get('/top-technicians', [OrderProductHistoryController::class, 'topTechnicians']);
    });

    Route::prefix('clinics')->group(function () {
        Route::get('/', [ClinicController::class, 'show']);
        Route::get('/{id}/doctors', [DoctorController::class, 'doctorsByClinic']);
    });

    Route::prefix('doctors')->group(function (){
        Route::get('/',[DoctorController::class,'doctors']);
        Route::get('/toggle-price-visibility/{doctorAccountId}',[DoctorController::class,'togglePriceVisibility']);
        Route::get('/toggle-financial-stats-visibility/{doctorAccountId}',[DoctorController::class,'toggleFinancialStatsVisibility']);
    });

    Route::prefix('invoice-header')->group(function (){
        Route::post('/',[LabHeaderController::class,'store']);
        Route::post('/update',[LabHeaderController::class,'update']);
        Route::get('/',[LabHeaderController::class,'getHeader']);
        Route::delete('/',[LabHeaderController::class,'delete']);
    });

});
