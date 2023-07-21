<?php

namespace Aliwebto\EasyPayment;

use Aliwebto\EasyPayment\Models\Payment;
use Aliwebto\EasyPayment\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
        $data["pay_url"] = URL::signedRoute("easy-payment.payment", [
            "id" => $transaction->id,
            "uuid" => base64_encode($transaction_uuid)
        ]);
        return $data;
    }

    private static function generate_unique_transaction_uuid(): \Ramsey\Uuid\UuidInterface
    {
        $uuid = Str::uuid();
        if (Transaction::where("transaction_uuid", $uuid)->count() > 0) {
            return self::generate_unique_transaction_uuid();
        }
        return $uuid;
    }

    public static function requestTransactionId($gateway_name, Transaction $transaction)
    {
        $driver_path = config("easy-payment.gateways." . $gateway_name . ".driver");
        $remainingAmount = $transaction->amount - $transaction->paidAmount;
        $amount = $remainingAmount > config("easy-payment.maxPaymentAmount") ? config("easy-payment.maxPaymentAmount") : $remainingAmount;
        $driver = new   $driver_path();

        $specificCard = is_null($transaction->specificCard) ? false : $transaction->specificCard;
        return $driver->pay($transaction->id, $amount, $transaction->description, $specificCard);
    }

    public static function markPaymentAsPayed(Payment $payment, $status_code): void
    {
        $payment->update([
            "paid_at" => now(),
            "status_code" => $status_code,
        ]);

        $paidAmount = 0;
        foreach ($payment->transaction->payments()->where("paid_at","!=",null)->get() as $payment ){
            $paidAmount += $payment->amount;
        }
        $transaction_update = [
            "paidAmount" => $paidAmount
        ];
        if ($paidAmount == $payment->transaction->amount) {
            $transaction_update["paid_at"] = now();
        }
        $payment->transaction->update($transaction_update);
    }
}
