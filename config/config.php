<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    "callbackURL" => "http://localhost:8000/easy-payment/verify",
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
