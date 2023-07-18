<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\Exeptions\PaypingException;
use Aliwebto\EasyPayment\Exeptions\RequiredConfigMissingException;
use Aliwebto\EasyPayment\Exeptions\ZarinpalException;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Payping extends DriverAbstract implements DriverInterface
{

    // real api routes
    protected string $api_url = "https://api.payping.ir/v1/";

    protected string $endpoint = "https://api.payping.ir/v1/pay/gotoipg/{authority}";


    /**
     * @throws RequiredConfigMissingException
     */
    public function pay(int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): Collection
    {
        throw_if($specificCard, "specific card not supported in payping");

        $authority = $this->getGatewayReferenceId($amount, $description, $specificCard, $email, $phone);
        return collect([
            "data" => [
                "authority" => $authority,
                "amount" => $amount,
                "description" => $description,
                "specificCard" => false,
                "email" => $email,
                "phone" => $phone
            ],
            "payment_url" => str_replace("{authority}", $authority, $this->endpoint)
        ]);
    }

    /**
     * @throws ZarinpalException
     * @throws RequiredConfigMissingException
     */
    public function verify(string $refId, $amount): bool
    {
        $this->checkRequiredParam("payping", [
            "token"
        ]);
        $token = config("easy-payment.gateways.payping.token");


        $data = [
            "amount" => $amount,
            "refId" => $refId,
        ];

        try {
            $request = Http::withHeaders([
                'Content-type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post($this->api_url . 'pay/verify', $data);
            if ($request->getStatusCode() >= 200 && $request->getStatusCode() < 300) {
                return true;
            }
        } catch (RequestException $re) {
            throw new PaypingException($re->getResponse()->getBody()->getContents(), $re->getResponse()->getStatusCode(), $re->getPrevious());
        } catch (\Exception $e) {
            throw new PaypingException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        return false;
    }

    /**
     * @throws RequiredConfigMissingException
     * @throws Exception
     */
    public function getGatewayReferenceId(int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): string
    {
        throw_if($specificCard, "specific card not supported in payping");
        $this->checkRequiredParam("payping", [
            "token"
        ]);
        $token = config("easy-payment.gateways.payping.token");
        $callbackURL = config("easy-payment.callbackURL");

        $data = [
            "amount" => $amount,
            "description" => $description,
            "returnUrl" => $callbackURL
        ];

        try {
            $request = Http::withHeaders([
                'Content-type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post($this->api_url . 'pay', $data);
            $result = json_decode($request->getBody()->getContents(), false);
            if (isset($result->Error)) {
                throw new PaypingException($result->Error);
            }
            if (isset($result->code)) {
                return $result->code;
            }
            throw new Exception('easy-payment: invalid response from gateway');


            } catch (HttpClientException $e) {
            throw new Exception('HttpError: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }

    }

}
