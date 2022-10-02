<?php

namespace TTSoft\MomoPay\Services\Abstracts;

use Botble\Support\Services\ProduceServiceInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use TTSoft\MomoPay\Services\Api\MomoPaymentApi;
use TTSoft\MomoPay\Services\Traits\MomoPaymentErrorTrait;

abstract class MomoPaymentAbstract implements ProduceServiceInterface
{
    use MomoPaymentErrorTrait;

    /**
     * @var MomoPaymentApi
     */
    protected $gateway;

    /**
     * MomoPaymentAbstract constructor.
     */
    public function __construct()
    {
        $gateway = new MomoPaymentApi();
        $this->gateway = $gateway;
        $this->gateway->initialize([
            'accessKey'   => setting('payment_momopay_access_key'),
            'partnerCode' => setting('payment_momopay_partner_code'),
            'secretKey'   => setting('payment_momopay_secret_key'),
            'testMode'    => setting('payment_momopay_mode') == '0',
        ]);
    }

    /**
     * Execute main service
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function execute(Request $request)
    {
        try {
            return $this->makePayment($request);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception);
            return false;
        }
    }

    /**
     * Make a payment
     *
     * @param Request $request
     *
     * @return mixed
     */
    abstract public function makePayment(Request $request);

    /**
     * Execute main service
     *
     * @param int $orderId
     *
     * @return false|string
     */
    public function hashOrderId($orderId)
    {
        try {
            Crypt::generateKey(MOMOPAY_PAYMENT_SLAT_HASHID);
            return Crypt::encrypt($orderId);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception);
            return false;
        }
    }

    /**
     * Execute main service
     *
     * @param $hashOrderId
     * @return mixed
     */
    public function deHashOrderId($hashOrderId)
    {
        try {
            Crypt::generateKey(MOMOPAY_PAYMENT_SLAT_HASHID);
            return Crypt::decrypt($hashOrderId);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception);
            return false;
        }
    }

    /**
     * Get payment status
     *
     * @param Request $request
     * @return array|false Object payment details or false
     */
    public function getPaymentStatus(Request $request)
    {
        if (empty($request->input('orderId')) || empty($request->input('requestId'))) {
            return false;
        }

        return $this->gateway->getPaymentStatus($request);
    }
}
