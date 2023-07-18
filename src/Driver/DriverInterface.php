<?php

namespace Aliwebto\EasyPayment\Driver;

use Illuminate\Support\Collection;

interface DriverInterface
{
    /**
     * @return bool
     */
    public function pay(int $amount, string $description, $specificCard = false, bool|string $email = false,bool|string $phone = false): Collection;

    /**
     * @return bool
     */
    public function verify(string $authority,$amount): bool;

    /**
     * @return string
     */
    public function getGatewayReferenceId(int $amount, string $description, bool|string $specificCard = false, bool|string $email = false,bool|string $phone = false): string;

}
