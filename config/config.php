<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    "site-name" => env("EASYPAYMENT_SITENAME","Easy Payment 💕"),
    "maxPaymentAmount" => env("EASYPAYMENT_MAX_PAYMENT_AMOUNT","50000000"),
    "callbackURL" => env("EASYPAYMENT_CALLBACK","http://localhost:8000/easy-payment/verify"),
    "gateways" => [
        "zarinpal" => [
            "merchantID" => env("EASYPAYMENT_ZARINPAL_MERCHANTID","123456789012345678901234567890123456"),
            "mode" => "sandbox" // default - zaringate - sandbox
        ],
        "payping" => [
            "token" => env("EASYPAYMENT_PAYPING_TOKEN","")
        ]
    ]
];
