<?php

namespace TTSoft\MomoPay\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Eloquent\PaymentRepository;
use Botble\Payment\Supports\PaymentHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;
use TTSoft\MomoPay\Services\Gateways\MomoPaymentService;

class MomoPaymentController extends Controller
{
    /**
     * @var MomoPaymentService
     */
    protected $momoPaymentService;

    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * PaymentController constructor.
     * @param MomoPaymentService $momoPaymentService
     */
    public function __construct(MomoPaymentService $momoPaymentService)
    {
        $this->momoPaymentService = $momoPaymentService;
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function checkStatusPaymentMomo(Request $request, BaseHttpResponse $response)
    {
        try {
            $this->paymentRepository = new PaymentRepository(new Payment());
            $responsePay = $this->momoPaymentService->getPaymentStatus($request);
            $orderId = json_decode($responsePay->extraData)->order_id;
            $transId = $responsePay->transId;

            if (!$transId) {
                Log::error('checkStatusPaymentMomo: transaction id momo service not found');

                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage('Transaction id momo service not found');
            }

            $checkTransaction = $this->paymentRepository->getFirstBy(['charge_id' => $transId]);

            if ($checkTransaction) {
                return $response
                    ->setError()
                    ->setNextUrl(PaymentHelper::getCancelURL())
                    ->setMessage('Transaction exits in system');
            }

            if ($responsePay->errorCode == 0) {
                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount'          => $responsePay->amount,
                    'charge_id'       => $transId,
                    'currency'        => 'VND',
                    'order_id'        => (array)$orderId,
                    'customer_id'     => auth('customer')->check() ? auth('customer')->id() : null,
                    'customer_type'   => 'Botble\Ecommerce\Models\Customer',
                    'payment_channel' => MOMOPAY_PAYMENT_METHOD_NAME,
                    'status'          => PaymentStatusEnum::COMPLETED,
                ]);

                return $response
                    ->setNextUrl(PaymentHelper::getRedirectURL())
                    ->setMessage(__('Checkout successfully!'));
            }

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage($responsePay->localMessage ?: __('Payment failed!'));

        } catch (Exception $e) {
            Log::error('checkStatusPaymentMomo:' . $e->getMessage());

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage($e->getMessage() ?: __('Payment failed!'));
        }
    }
}
