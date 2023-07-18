<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\Exeptions\RequiredConfigMissingException;
use Aliwebto\EasyPayment\Exeptions\ZarinpalException;
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
    protected string $sandboxEndPoint = 'https://sandbox.zarinpal.com/pg/StartPay/{authority}';
    protected string $sandboxRequestAPI = "https://sandbox.zarinpal.com/pg/v4/payment/request.json";
    protected string $sandboxVerifyAPI = "https://sandbox.zarinpal.com/pg/v4/payment/verify.json";

    /**
     * @throws RequiredConfigMissingException
     */
    public function pay(int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): Collection
    {
        $authority = $this->getGatewayReferenceId($amount, $description, $specificCard, $email, $phone);
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
     */
    public function verify(string $authority, $amount): bool
    {
        $this->checkRequiredParam("zarinpal", [
            "merchantID"
        ]);
        $merchantId = config("easy-payment.gateways.zarinpal.merchantID");
        $mode = config("easy-payment.gateways.zarinpal.mode");

        $requestAPI = ($mode == "sandbox") ? $this->sandboxVerifyAPI : $this->verifyAPI;

        $data = [
            "merchant_id" => $merchantId,
            "amount" => $amount,
            "authority" => $authority,
        ];

        try {
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);
            if (isset($response["errors"]) and sizeof($response["errors"]) > 0) {
                throw new ZarinpalException($response["errors"]["code"], $response["errors"]["message"]);
            } else {
                $response = $response["data"];
                if ($response["message"] == "Verified") {
                    if ($response["code"] == 100 or $response["code"] == 101) {
                        // TODO mark transaction as successful
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

    /**
     * @throws RequiredConfigMissingException
     * @throws Exception
     */
    public function getGatewayReferenceId(int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): string
    {
        $this->checkRequiredParam("zarinpal", [
            "merchantID"
        ]);
        $merchantId = config("easy-payment.gateways.zarinpal.merchantID");
        $mode = config("easy-payment.gateways.zarinpal.mode");
        $callbackURL = config("easy-payment.callbackURL");

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
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);

            if (isset($response["errors"]) and sizeof($response["errors"]) > 0) {
                throw new ZarinpalException($response["errors"]["code"], $response["errors"]["message"]);
            } else {
                $response = $response["data"];
                if ($response["message"] == "Success") {
                    if ($response["code"] == 100) {
                        // TODO set payment transaction id in db
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
