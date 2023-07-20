<?php

namespace Aliwebto\EasyPayment\Http\Controllers;

use Aliwebto\EasyPayment\Models\Transaction;
use Illuminate\Http\Request;

class EasyPaymentController
{
    public function pay(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        $validData = $request->validate([
            "id" => "required",
            "uuid" => "required"
        ]);
        $id = $validData["id"];
        $uuid = base64_decode($validData["uuid"]);
        $transaction = Transaction::where("id", $id)->where("transaction_uuid", $uuid)->firstOrFail();
        $paymentsCount = ceil($transaction->amount / config("easy-payment.maxPaymentAmount"));
        $remainingAmount = $transaction->amount - $transaction->paidAmount;
        $remainingPaymentsCount = ceil($remainingAmount / config("easy-payment.maxPaymentAmount"));


        return view("easy-payment::pay", [
            "transaction" => $transaction,
            "paymentsCount" => $paymentsCount,
            "remainingAmount" => $remainingAmount,
            "remainingPaymentsCount" => $remainingPaymentsCount
        ]);
    }
}
