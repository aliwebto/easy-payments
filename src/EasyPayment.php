<?php

namespace Aliwebto\EasyPayment;

use Aliwebto\EasyPayment\Driver\Zarinpal;

class EasyPayment
{
    public static function pay()
    {
        $gateway = new Zarinpal();
        return $gateway->pay(10000, "payment for user 1");
    }
}
