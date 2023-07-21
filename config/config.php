<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    "site-name" => env("EASYPAYMENT_SITENAME", "Easy Payment 💕"),
    "maxPaymentAmount" => env("EASYPAYMENT_MAX_PAYMENT_AMOUNT", "500000000"),
    "returnAfterComplete" => env("EASYPAYMENT_RETURN_PATH", "/"),
    "callbackURL" => env("EASYPAYMENT_CALLBACK", "http://localhost:8888/easy-payment/verify"),
    "gateways" => [
    "zarinpal" => [
        "driver" => \Aliwebto\EasyPayment\Driver\Zarinpal::class,
        "merchantID" => env("EASYPAYMENT_ZARINPAL_MERCHANTID", "123456789012345678901234567890123456"),
        "mode" => "sandbox" // default - zaringate - sandbox
    ]
]
];
