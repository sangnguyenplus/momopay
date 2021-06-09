<?php

namespace TTSoft\MomoPay\Services\Gateways;

use Exception;
use Illuminate\Http\Request;
use Log;
use TTSoft\MomoPay\Services\Abstracts\MomoPaymentAbstract;
use TTSoft\MomoPay\Services\Traits\MomoPaymentErrorTrait;

class MomoPaymentService extends MomoPaymentAbstract
{
    use MomoPaymentErrorTrait;

    /**
     * Make a payment
     *
     * @param Request $request
     *
     * @return mixed
     * @throws Exception
     */
    public function makePayment(Request $request)
    {
        try {
            $response = $this->gateway->purchase([
                'amount'    => (string)convert_amount_to_VND($request->get('amount')),
                'returnUrl' => route('payments.momo.status'),
                'notifyUrl' => route('payments.momo.status'),
                'orderId'   => uniqid(),
                'requestId' => $request->get('token'),
                'extraData' => '{"order_id":' . $request->get('order_id') . '}',
            ]);
            if ($this->gateway->isRedirect($response)) {
                return $this->gateway->getRedirectUrl($response);
            }

            $this->setErrorMessage($response->localMessage);

            return false;
        } catch (Exception $e) {
            Log::error('MomoPaymentService - makePayment: ' . $e->getMessage());
            $this->setErrorMessage($response->localMessage);

            return false;
        }
    }
}
