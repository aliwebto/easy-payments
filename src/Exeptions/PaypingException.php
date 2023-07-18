<?php

namespace Aliwebto\EasyPayment\Exeptions;

use Exception;
use Throwable;

class PaypingException extends Exception
{
    protected $message;

    public function __construct($message,$code="undefined", Throwable $previous = null)
    {
        $this->message = $message;

        parent::__construct("Payping Error : Code : $code : $message", 0, $previous);
    }

}
