<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LabHeaderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderFileController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderProductHistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\ToothColorController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Zatca\InvoiceController;
use App\Http\Controllers\Zatca\ZatcaOnboardingController;
use Illuminate\Support\Facades\Route;

Route::post('/register-company', [UserController::class, 'registerCompany']);


Route::middleware(['auth:admin', 'admin.role','check.subscriber'])->group(function () {
    Route::prefix('types')->group(function (){
        Route::post('/',[TypeController::class,'createType']);
        Route::get('/', [TypeController::class, 'listTypes']);
        Route::delete('/{id}',[TypeController::class,'destroy']);
    });

    Route::prefix('invoice-header')->group(function (){
        Route::post('/',[LabHeaderController::class,'store']);
        Route::post('/update',[LabHeaderController::class,'update']);
        Route::get('/',[LabHeaderController::class,'getHeader']);
        Route::delete('/',[LabHeaderController::class,'delete']);
    });

    Route::prefix('order-files')->group(function () {
        Route::post('/upload', [OrderFileController::class, 'createUpload']);
        Route::post('/{id}/uploaded', [OrderFileController::class, 'markUploaded']);
        Route::get('/{id}/download', [OrderFileController::class, 'download']);
    });

    Route::prefix('profile')->group(function (){
        Route::put('/updateAddress',[SubscriberController::class,'updateAddress']);
        Route::get('/',[UserController::class,'adminProfile']);
        Route::put('/',[UserController::class,'adminUpdateProfile']);
        Route::delete('/',[UserController::class,'deleteAccount']);
    });

    Route::prefix('clinics')->group(function (){
        Route::post('/', [ClinicController::class, 'store']);
        Route::get('/', [ClinicController::class, 'show']);
        Route::put('/updateAddress/{clinicId}',[ClinicController::class,'updateAddress']);
        Route::put('/{id}', [ClinicController::class, 'edit']);
        Route::delete('/{id}', [ClinicController::class, 'destroy']);
    });

    Route::prefix('doctors')->group(function (){
        Route::post('/', [DoctorController::class, 'store']);
        Route::get('/{id}', [DoctorController::class, 'show']);
        Route::delete('/{id}', [DoctorController::class, 'destroy']);
        Route::get('/toggle-price-visibility/{doctorAccountId}',[DoctorController::class,'togglePriceVisibility']);
        Route::get('toggle-financial-stats-visibility/{doctorAccountId}',[DoctorController::class,'toggleFinancialStatsVisibility']);
    });

    Route::prefix('specializations')->group(function (){
        Route::post('/', [SpecializationController::class, 'store']);
        Route::get('/', [SpecializationController::class, 'getSpecializationsBySubscriber']);
        Route::delete('/{id}',[SpecializationController::class,'delete']);
    });

    Route::prefix('orders')->group(function (){
        Route::get('/filters',[OrderController::class,'OrdersWithFilters']);
        Route::post('/',[OrderController::class,'createOrder']);
        Route::put('/{id}',[OrderController::class,'updateOrder']);
        Route::post('/invoice/bulk',[InvoiceController::class,'invoiceBulk'])->middleware('check.zatca');
        Route::post('credit-note',[InvoiceController::class,'submitCreditNote'])->middleware('check.zatca');
        Route::get('/{id}/details',[OrderController::class,'orderDetails']);
        Route::prefix('order-products')->group(function (){
            Route::prefix('history')->group(function (){
                Route::get('/', [OrderProductHistoryController::class, 'index']);
                Route::get('/{orderProductId}', [OrderProductHistoryController::class, 'orderProductHistory']);
            });
            Route::post('',[OrderProductController::class,'store']);
            Route::put('/{id}',[OrderProductController::class,'update']);
            Route::delete('/{id}',[OrderProductController::class,'destroy']);
        });
    });
    Route::prefix('reports')->group(function () {
            Route::get('/revenue', [ReportController::class,'revenue']);
        Route::get('/doctors-due', [ReportController::class,'doctorsDue']);
        Route::get('/top-technicians', [OrderProductHistoryController::class, 'topTechnicians']);
    });
    Route::post('doctor-orders',[OrderController::class,'listDoctorInvoices']);
    Route::post('from-to-orders',[OrderController::class,'listFromToInvoices']);
    Route::post('add-category',[CategoryController::class,'store']);
    Route::get('show-categories', [CategoryController::class,'show']);
    Route::get('delete-category/{id}',[CategoryController::class,'delete']);
    Route::post('add-product',[ProductController::class,'store']);
    Route::get('showProductsByCategory/{category_id}',[ProductController::class,'showByCategory']);
    Route::post('edit-category',[CategoryController::class,'update']);
    Route::delete('delete-product/{id}',[ProductController::class,'delete']);
    Route::patch('/products/{id}', [ProductController::class, 'updateName']);
    Route::post('update-price',[ProductController::class,'updatePrice']);
    Route::post('add-toothColor',[ToothColorController::class,'add']);
    Route::get('delete-toothColor/{id}',[ToothColorController::class,'delete']);
    Route::get('show-toothColor',[ToothColorController::class,'show']);
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get-doctors',[DoctorController::class,'getDoctors']);
    Route::get('get-technicals',[UserController::class,'getTechnical']);
    Route::post('add-specialization',[SpecializationController::class,'addSpecialization']);

    Route::get('doctorsByClinic/{id}', [DoctorController::class, 'doctorsByClinic']);



    Route::post('add-special-price', [ClinicController::class, 'addSpecialPrice']);
    Route::delete('delete-special-price', [ClinicController::class, 'deleteSpecialPrice']);
    Route::patch('availability/{id}', [UserController::class, 'setAvailability']);
    Route::get('clinics_with_special_price/{$subscriberId}',[ClinicController::class,'clinics_with_special_price']);
    Route::get('get_clinics_with_the_special_price/{id}',[ProductController::class,'get_clinics_with_the_special_price']);



    Route::get('cancel-subscription',[SubscriberController::class,'cancelSubscription']);
    Route::post('add-payment',[OrderController::class,'addPayment']);
    Route::patch('order-products/{id}/assign-specialization', [OrderController::class, 'assignSpecialization']);
    Route::get('order-details',[OrderController::class,'orderDetails']);
    Route::post('apply-discount',[OrderController::class,'applyDiscount']);
    Route::put('update-discount',[OrderController::class,'updateDiscount']);
    Route::delete('remove-discount/{discount_id}',[OrderController::class,'removeDiscount']);


});

Route::middleware(['auth:admin', 'admin.role'])->group(function () {
    Route::get('plans', [SubscriptionPlanController::class, 'index']);
    Route::get('remaining-days',[SubscriberController::class,'remainingDays']);
    Route::prefix('contact-info')->group(function () {
        Route::get('/', [ContactInfoController::class, 'index']);
    });
    Route::prefix('zatca')->group(function (){
        Route::post('onboard',[ ZatcaOnboardingController::class,'store']);
        Route::post('renew',[ZatcaOnboardingController::class,'renew']);
    });
});



