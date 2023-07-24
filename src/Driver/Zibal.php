<?php

namespace Aliwebto\EasyPayment\Driver;

use Aliwebto\EasyPayment\EasyPayment;
use Aliwebto\EasyPayment\Exeptions\RequiredConfigMissingException;
use Aliwebto\EasyPayment\Models\Payment;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Zibal extends DriverAbstract implements DriverInterface
{

    // real payment routes
    protected string $endPoint = 'https://gateway.zibal.ir/start/{authority}';

    // real api routes
    protected string $requestAPI = "https://gateway.zibal.ir/v1/request";
    protected string $verifyAPI = "https://gateway.zibal.ir/v1/verify";


    public $payment;

    /**
     * @throws RequiredConfigMissingException
     */
    public function pay($transaction_id, int $amount, string $description, $specificCard = false, bool|string $email = false, bool|string $phone = false): Collection
    {
        $authority = $this->getGatewayReferenceId($transaction_id, $amount, $description, $specificCard, $email, $phone);
        $payment_url = $this->endPoint;
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
     * @throws RequiredConfigMissingException
     * @throws Exception
     */
    public function verify($request): bool
    {
        $this->checkRequiredParam("zibal", [
            "merchantID"
        ]);

        // get authority from request
        $authority = request()->get("trackId");
        $transactionModelId = request()->get("orderId");
        $payment = Payment::where("gateway_transaction_id", $authority)->where("transaction_id", $transactionModelId)->with("transaction")->first();

        // throw if payment not exist
        throw_if(is_null($payment), "Payment Not Found");

        // set payment to this class for global access
        $this->payment = $payment;
        $merchantId = config("easy-payment.gateways.zibal.merchantID");

        $requestAPI = $this->verifyAPI;

        $data = [
            "merchant" => $merchantId,
            "trackId" => $authority
        ];

        try {
            // request to zibal for verify payment
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);

            // status code 100 mean verify successful and status code 201 mean verified before and other status codes mean payment has error
            if ($response["result"] == 100 or $response["result"] == 201) {
                EasyPayment::markPaymentAsPayed($payment, $response["result"]);
                return true;
            } else {
                $payment->update([
                    "status_code" => $response["result"],
                ]);
                throw new Exception($response["result"]);
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
        $this->checkRequiredParam("zibal", [
            "merchantID"
        ]);
        $merchantId = config("easy-payment.gateways.zibal.merchantID");
        $callbackURL = config("easy-payment.callbackURL") . "/zibal";

        $requestAPI = $this->requestAPI;

        $data = [
            "merchant" => $merchantId,
            "amount" => $amount,
            "description" => $description,
            "callbackUrl" => $callbackURL,
            "orderId" => $transaction_id
        ];
        if ($specificCard) {
            $data["allowedCards"] = [$specificCard];
        }
        if ($phone) {
            $data["metadata"]["mobile"] = $phone;
        }


        try {
            // request trackId from zibal api
            $request = Http::post($requestAPI, $data);
            $response = json_decode($request->body(), true);


            if ($response["result"] == 100) {
                // response code 100 mean track id get successful
                Payment::create([
                    "gateway_name" => "zibal",
                    "amount" => $amount,
                    "transaction_id" => $transaction_id,
                    "gateway_transaction_id" => $response["trackId"],
                ]);
                return $response["trackId"];
            }
            // error in getting trackId
            throw new Exception($response["message"]);
        } catch (HttpClientException $e) {
            throw new Exception('HttpError: ' . $e->getMessage() . ' #' . $e->getCode(), $e->getCode());
        }

    }

}
