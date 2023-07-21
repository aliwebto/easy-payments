<?php

use Aliwebto\EasyPayment\Http\Controllers\EasyPaymentController;
use \Illuminate\Support\Facades\Route;


Route::group(["prefix" => "easy-payment/"], function () {
    Route::get("payment", [EasyPaymentController::class, "payment"])->name("easy-payment.payment");
    Route::get("pay",[EasyPaymentController::class,"pay"])->name("easy-payment.pay");
    Route::get("verify/{gateway_name}",[EasyPaymentController::class,"verify"])->name("easy-payment.verify");
});
