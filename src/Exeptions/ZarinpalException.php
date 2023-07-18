<?php

namespace Aliwebto\EasyPayment\Exeptions;

use Exception;
use Throwable;

class ZarinpalException extends Exception
{
    protected $code;
    protected $message;

    public function __construct($code, $message, Throwable $previous = null)
    {
        $this->code = $code;
        $this->message = $message;

        parent::__construct("Zarinpal Error Code : $code : $message", 0, $previous);
    }

}
