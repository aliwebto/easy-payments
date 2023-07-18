<?php

namespace Aliwebto\EasyPayment\Exeptions;

use Exception;
use Throwable;

class RequiredConfigMissingException extends Exception
{
    protected $missing;

    public function __construct($missing, $gateway, Throwable $previous = null)
    {
        $this->missing = $missing;

        parent::__construct("param $missing from $gateway missing",0, $previous);
    }


}
