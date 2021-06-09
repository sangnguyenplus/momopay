<?php

namespace TTSoft\MomoPay\Services\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class MomoPaymentApi
{
    /**
     * @var array
     */
    protected $defaultParameter = [
        'partnerCode',
        'accessKey',
        'testMode',
    ];

    /**
     * Init default parameter
     *
     * @return void
     */
    public function initialize($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Make a purchase
     *
     * @param array $data
     *
     * @return array
     */
    public function purchase($data)
    {
        $data['requestType'] = 'captureMoMoWallet';
        $data['orderInfo'] = '';
        $data = $this->getDefaultParameter($data);

        $data['signature'] = $this->getPurchaseSignature($data);
        $json = self::requestApi($data);

        return json_decode($json);
    }

    /**
     * Get partnerCode && accessKey
     *
     * @param $data
     *
     * @return mixed
     */
    private function getDefaultParameter($data)
    {
        foreach ($this->defaultParameter as $key) {
            if ($this->$key) {
                $data[$key] = $this->$key;
            }
        }

        return $data;
    }

    /**
     * Get purchase signature
     *
     * @param $data
     *
     * @return string
     */
    private function getPurchaseSignature($data)
    {
        $string = 'partnerCode=' . $this->partnerCode .
            '&accessKey=' . $this->accessKey .
            '&requestId=' . $data['requestId'] .
            '&amount=' . $data['amount'] .
            '&orderId=' . $data['orderId'] .
            '&orderInfo=' .
            '&returnUrl=' . $data['returnUrl'] .
            '&notifyUrl=' . $data['notifyUrl'] .
            '&extraData=' . $data['extraData'];

        return hash_hmac('sha256', $string, $this->secretKey);
    }

    /**
     * Call api
     *
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    private static function requestApi($data)
    {
        try {
            $testMode = $data['testMode'] ?? false;
            unset($data['testMode']);

            if ($testMode) {
                $response = Http::post(self::getDomainApi(), $data);
            } else {
                $response = Http::post(self::getProductDomainApi(), $data);
            }

            if ($response->ok()) {
                return $response->body();
            }

            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * Get momo api domain
     *
     * @return string
     */
    private static function getDomainApi()
    {
        return config('plugins.momopay.general.domain_api_sandbox');
    }

    private static function getProductDomainApi()
    {
        return config('plugins.momopay.general.domain_api_product');
    }

    /**
     * Get payment status
     *
     * @param Request $request
     *
     * @return array
     */
    public function getPaymentStatus($request)
    {
        $data = [
            'requestType' => 'transactionStatus',
            'orderId'     => $request->input('orderId'),
            'requestId'   => $request->input('requestId'),
        ];

        $data = $this->getDefaultParameter($data);
        $data['signature'] = $this->getPaymentStatusSignature($data);

        $json = self::requestApi($data);

        return json_decode($json);
    }

    /**
     * Get payment status signature
     *
     * @param array $data
     *
     * @return string
     */
    private function getPaymentStatusSignature($data)
    {
        $string = 'partnerCode=' . $this->partnerCode .
            '&accessKey=' . $this->accessKey .
            '&requestId=' . $data['requestId'] .
            '&orderId=' . $data['orderId'] .
            '&requestType=transactionStatus';

        return hash_hmac('sha256', $string, $this->secretKey);
    }

    /**
     * Check if call api success
     *
     * @param $response
     *
     * @return boolean
     */
    public function isRedirect($response)
    {
        return $response->errorCode == 0 && $this->checkSignature($response);
    }

    /**
     * Check data of response
     *
     * @param $data
     *
     * @return boolean
     */
    private function checkSignature($data)
    {
        $string = 'requestId=' . $data->requestId .
            '&orderId=' . $data->orderId .
            '&message=' . $data->message .
            '&localMessage=' . $data->localMessage .
            '&payUrl=' . $data->payUrl .
            '&errorCode=' . $data->errorCode .
            '&requestType=' . $data->requestType;

        return hash_hmac('sha256', $string, $this->secretKey) == $data->signature;
    }

    /**
     * Get redirect Url
     *
     * @param $response
     *
     * @return string
     */
    public function getRedirectUrl($response)
    {
        return $response->payUrl;
    }
}
