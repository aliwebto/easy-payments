<?php

use Aliwebto\EasyPayment\Http\Controllers\EasyPaymentController;
use \Illuminate\Support\Facades\Route;


Route::group(["prefix" => "easy-payment/"], function () {
    Route::get("pay", [EasyPaymentController::class, "pay"])->name("easy-payment.pay");
});
