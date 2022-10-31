<?php

use Illuminate\Support\Facades\Route;
use Mimocodes\Payment\Controllers\PaymentController;


Route::post('/mimocodes/payment/opay/payment-callback-url',[PaymentController::class,'callback'])->name('opay.callback');

