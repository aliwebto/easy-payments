<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\Exeptions\RequiredConfigMissingException;

abstract class DriverAbstract
{
    protected string $configPrefix = "easy-payment";

    public function checkRequiredParam($gateway_name, $required_parameters): bool
    {
        foreach ($required_parameters as $required_parameter) {
            if (is_null(config($this->configPrefix.".gateways.".$gateway_name.".".$required_parameters[0]))) {
                throw new RequiredConfigMissingException($required_parameter, $gateway_name);
            }
        }
        return true;
    }
}
