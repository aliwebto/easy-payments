<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\Models\Payment;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;

interface DriverInterface
{
    /**
     * @return bool
     */
    public function pay($transaction_id,int $amount, string $description, $specificCard = false, bool|string $email = false,bool|string $phone = false): Collection;

    /**
     * @return bool
     */
    public function verify(Request $request): bool;

    /**
     * @return string
     */
    public function getGatewayReferenceId($transaction_id,int $amount, string $description, bool|string $specificCard = false, bool|string $email = false,bool|string $phone = false): string;

}
