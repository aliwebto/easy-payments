<?php

namespace Aliwebto\EasyPayment;

use Aliwebto\EasyPayment\Driver\Zarinpal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class EasyPayment
{

    public static function pay(Model $payableModel, int $amount, $description, $specificCard = null)
    {
        throw_if(is_null($payableModel->transactions), "model " . class_basename($payableModel) . " not payable");
        $paymentsCount = $amount / config("easy-payment.maxPaymentAmount");
        $transaction_uuid = self::generate_unique_transaction_uuid();
        $data = [
            "transaction_uuid" => $transaction_uuid,
            "amount" => $amount,
            "description" => $description,
            "specificCard" => $specificCard
        ];
        $transaction = $payableModel->transactions()->create($data);
        $data["payments_count"] = ceil($paymentsCount);
        $data["id"] = $transaction->id;
        $data["pay_url"] = URL::signedRoute("easy-payment.pay", [
            "id" => $transaction->id,
            "uuid" => base64_encode($transaction_uuid)
        ]);
        return $data;
    }

    private static function generate_unique_transaction_uuid(): \Ramsey\Uuid\UuidInterface
    {
        $uuid = \Illuminate\Support\Str::uuid();
        if (\Aliwebto\EasyPayment\Models\Transaction::where("transaction_uuid", $uuid)->count() > 0) {
            return self::generate_unique_transaction_uuid();
        }
        return $uuid;
    }
}
