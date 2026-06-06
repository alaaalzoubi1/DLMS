<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LabHeaderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderProductHistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Zatca\InvoiceController;
use App\Http\Controllers\Zatca\ZatcaOnboardingController;
use Illuminate\Support\Facades\Route;

Route::post('register',[AccountantController::class,'registerAccountant']);
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/password/forgot', [ForgotPasswordController::class, 'requestResetCode']);
Route::middleware(['auth:admin','accountant.role','check.subscriber'])->group(function ()
{
    Route::prefix('types')->group(function (){
        Route::post('/',[TypeController::class,'createType']);
        Route::get('/', [TypeController::class, 'listTypes']);
        Route::delete('/{id}',[TypeController::class,'destroy']);
    });
    Route::get('home-page',[SubscriberController::class,'dashboardStats']);
    Route::get('logout',[UserController::class,'logout']);
    Route::get('me',[AccountantController::class,'accountantProfile']);
    Route::delete('account',[AccountantController::class,'deleteAccount']);
    Route::post('addPayment',[OrderController::class,'addPayment']);
    Route::post('add-payment/doctor',[OrderController::class,'addPaymentDoctor']);
    Route::post('add-payment/clinic',[OrderController::class,'addPaymentClinic']);

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
        Route::get('/filters/all',[OrderController::class,'OrdersWithFiltersAll']);
        Route::get('/filters',[OrderController::class,'OrdersWithFilters']);
        Route::post('/',[OrderController::class,'createOrder']);

        Route::put('/{id}',[OrderController::class,'updateOrder']);
        Route::post('/invoice/bulk',[InvoiceController::class,'invoiceBulk'])->middleware('check.zatca');
        Route::post('/credit-note',[InvoiceController::class,'submitCreditNote'])->middleware('check.zatca');
        Route::get('/credit-note',[CreditNoteController::class,'index']);
        Route::get('/credit-note/{id}',[CreditNoteController::class,'show']);
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
    Route::prefix('zatca')->group(function (){
        Route::post('onboard',[ ZatcaOnboardingController::class,'store']);
        Route::post('renew',[ZatcaOnboardingController::class,'renew']);
    });

});
