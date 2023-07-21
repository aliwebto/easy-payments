<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\EasyPayment;
use Aliwebto\EasyPayment\Exeptions\RequiredConfigMissingException;
use Aliwebto\EasyPayment\Exeptions\ZarinpalException;
use Aliwebto\EasyPayment\Models\Payment;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Zarinpal extends DriverAbstract implements DriverInterface
{

    // real payment routes
    protected string $endPoint = 'https://www.zarinpal.com/pg/StartPay/{authority}';
    protected string $zarinEndPoint = 'https://www.zarinpal.com/pg/StartPay/{authority}/ZarinGate';


    // real api routes
    protected string $requestAPI = "https://api.zarinpal.com/pg/v4/payment/request.json";
    protected string $verifyAPI = "https://api.zarinpal.com/pg/v4/payment/verify.json";


    // sandbox for testing mode
    protected string $sandboxEndPoint = 'https://sandbox.banktest.ir/zarinpal/www.zarinpal.com/pg/StartPay/{authority}';
    protected string $sandboxRequestAPI = "https://sandbox.banktest.ir/zarinpal/api.zarinpal.com/pg/v4/payment/request.json";
    protected string $sandboxVerifyAPI = "https://sandbox.banktest.ir/zarinpal/api.zarinpal.com/pg/v4/payment/verify.json";

    public $payment;
    /**
     * @throws RequiredConfigMissingException
     */
    public function pay($transaction_id, int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): Collection
    {
        $authority = $this->getGatewayReferenceId($transaction_id, $amount, $description, $specificCard, $email, $phone);
        $mode = config("easy-payment.gateways.zarinpal.mode");
        $payment_url = "";
        switch ($mode) {
            case "default":
                $payment_url = $this->endPoint;
                break;
            case "zaringate":
                $payment_url = $this->zarinEndPoint;
                break;
            case "sandbox":
                $payment_url = $this->sandboxEndPoint;
                break;

        }
        return collect([
            "data" => [
                "authority" => $authority,
                "amount" => $amount,
                "description" => $description,
                "specificCard" => $specificCard,
                "email" => $email,
                "phone" => $phone
            ],
            "payment_url" => str_replace("{authority}", $authority, $payment_url)
        ]);
    }

    /**
     * @throws ZarinpalException
     * @throws RequiredConfigMissingException
     * @throws Exception
     */
    public function verify($request): bool
    {
        $this->checkRequiredParam("zarinpal", [
            "merchantID"
        ]);

        // get authority from request
        $authority = request()->get("Authority");
        $payment = Payment::where("gateway_transaction_id",$authority)->with("transaction")->first();

        // throw if payment not exist
        throw_if(is_null($payment),"Payment Not Found");

        // set payment to this class for global access
        $this->payment = $payment;
        $amount = $payment->amount;
        $merchantId = config("easy-payment.gateways.zarinpal.merchantID");
        $mode = config("easy-payment.gateways.zarinpal.mode");

        $requestAPI = ($mode == "sandbox") ? $this->sandboxVerifyAPI : $this->verifyAPI;

        $data = [
            "merchant_id" => $merchantId,
            "amount" => $amount,
            "authority" => $authority,
        ];

        try {
            // request to zarinpal for verify paymenr
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);

            // if there is an error throw it
            if (isset($response["errors"]) and sizeof($response["errors"]) > 0) {
                throw new ZarinpalException($response["errors"]["code"], $response["errors"]["message"]);
            } else {
                // if there aren't any error run this part
                $response = $response["data"];
                // status code 100 mean verify successful and status code 101 mean verified before and other status codes mean payment has error
                if ($response["code"] == 100 or $response["code"] == 101) {
                    EasyPayment::markPaymentAsPayed($payment,$response["code"]);
                    return true;
                } else {
                    $payment->update([
                        "status_code" => $response["code"],
                    ]);
                    throw new Exception($response["code"]);
                }

            }


        } catch (HttpClientException $e) {
            throw new Exception('HttpError: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }
    }

    /**
     * @throws RequiredConfigMissingException
     * @throws Exception
     */
    public function getGatewayReferenceId($transaction_id, int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): string
    {
        $this->checkRequiredParam("zarinpal", [
            "merchantID"
        ]);
        $merchantId = config("easy-payment.gateways.zarinpal.merchantID");
        $mode = config("easy-payment.gateways.zarinpal.mode");
        $callbackURL = config("easy-payment.callbackURL") . "/zarinpal";

        $requestAPI = ($mode == "sandbox") ? $this->sandboxRequestAPI : $this->requestAPI;

        $data = [
            "merchant_id" => $merchantId,
            "amount" => $amount,
            "description" => $description,
            "callback_url" => $callbackURL
        ];
        if ($specificCard) {
            $data["card_pan"] = $specificCard;
        }
        if ($email) {
            $data["metadata"]["email"] = $email;
        }
        if ($phone) {
            $data["metadata"]["phone"] = $phone;
        }


        try {
            // request transactionId from zarinpal api
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);

            // if there is an error throw it
            if (isset($response["errors"]) and sizeof($response["errors"]) > 0) {
                throw new ZarinpalException($response["errors"]["code"], $response["errors"]["message"]);
            } else {
                // if there aren't any error run this part
                $response = $response["data"];
                if ($response["message"] == "Success") {
                    if ($response["code"] == 100) {
                        // response code 100 mean transaction id get successful
                        Payment::create([
                            "gateway_name" => "zarinpal",
                            "amount" => $amount,
                            "transaction_id" => $transaction_id,
                            "gateway_transaction_id" => $response["authority"],
                        ]);
                        return $response["authority"];
                    } else {
                        throw new Exception($response["code"]);
                    }
                }
            }
            throw new Exception('easy-payment: invalid response from gateway');


        } catch (HttpClientException $e) {
            throw new Exception('HttpError: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }

    }

}
