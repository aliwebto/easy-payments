<?php

namespace Aliwebto\EasyPayment\Http\Controllers;

use Aliwebto\EasyPayment\Driver\Zarinpal;
use Aliwebto\EasyPayment\EasyPayment;
use Aliwebto\EasyPayment\Models\Payment;
use Aliwebto\EasyPayment\Models\Transaction;
use http\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class EasyPaymentController
{
    public function payment(Request $request)
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
        $transaction = Transaction::where("id", $id)->where("transaction_uuid", $uuid)->with("payments")->firstOrFail();
        $paymentsCount = ceil($transaction->amount / config("easy-payment.maxPaymentAmount"));
        $remainingAmount = $transaction->amount - $transaction->paidAmount;
        $remainingPaymentsCount = ceil($remainingAmount / config("easy-payment.maxPaymentAmount"));
        Session::forget("easy-payment_transaction-uuid");
        Session::push("easy-payment_transaction-uuid", $uuid);

        return view("easy-payment::pay", [
            "transaction" => $transaction,
            "paymentsCount" => $paymentsCount,
            "remainingAmount" => $remainingAmount,
            "remainingPaymentsCount" => $remainingPaymentsCount
        ]);
    }

    public function pay(Request $request)
    {
        $validData = $request->validate([
            "gateway_name" => "required",
            "uuid" => "required"
        ]);
        $uuid = base64_decode($validData["uuid"]);
        $gateway_name = $validData['gateway_name'];
        abort_if(is_null(config("easy-payment.gateways." . $gateway_name)), 403);

        $transaction = Transaction::where("transaction_uuid", $uuid)->firstOrFail();
        abort_if(!is_null($transaction->paid_at), 403, "This transaction is completed");
        return EasyPayment::requestTransactionId($gateway_name, $transaction);
    }

    public function verify($gateway_name)
    {
        abort_if(is_null(config("easy-payment.gateways." . $gateway_name)), 403);
        $gateway_class = config("easy-payment.gateways." . $gateway_name . ".driver");
        $gateway_class = new $gateway_class;

        try {
            $gateway_class->verify(request());
            $status = "success";
            $message = "پرداخت شما با موفقیت انجام شد";
        } catch (\Exception $exception) {
            $status = "danger";
            $message = "در پرداخت شما خطایی رخ داده و در صورتی که حساب شما کسر شده باشد نهایتا تا 72 ساعت به حساب شما باز میگردد";
        }

        $payment = $gateway_class->payment;
        return redirect()->signedRoute("easy-payment.payment", [
            "id" => $payment->transaction->id,
            "uuid" => base64_encode($payment->transaction->transaction_uuid),
            "status" => $status,
            "message" => $message
        ]);
    }
}
