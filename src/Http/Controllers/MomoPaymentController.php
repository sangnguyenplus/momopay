<?php

namespace TTSoft\MomoPay\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Eloquent\PaymentRepository;
use Botble\Payment\Services\Traits\PaymentTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;
use OrderHelper;
use TTSoft\MomoPay\Services\Gateways\MomoPaymentService;

class MomoPaymentController extends Controller
{
    use PaymentTrait;

    /**
     * @var MomoPaymentService
     */
    protected $momoPaymentService;

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
            if (!$transId || $transId === "") {
                Log::error('checkStatusPaymentMomo: transaction id momo service not found');
                abort(500, 'Transaction id momo service not found');
            }
            $checkTransaction = $this->paymentRepository->getFirstBy(['charge_id' => $transId]);

            if ($checkTransaction) {
                abort(500, 'Transaction exsits in system');
            }
            if ($responsePay->errorCode == 0) {
                $this->storeLocalPayment([
                    'amount'          => $responsePay->amount,
                    'charge_id'       => $transId,
                    'currency'        => 'VND',
                    'payment_channel' => MOMOPAY_PAYMENT_METHOD_NAME,
                    'status'          => PaymentStatusEnum::COMPLETED,
                    'customer_id'     => auth('customer')->check() ? auth('customer')->user()
                        ->getAuthIdentifier() : null,
                    'payment_type'    => $responsePay->payType,
                    'order_id'        => $orderId,
                ]);

                OrderHelper::processOrder($orderId, $transId);

                return $response
                    ->setNextUrl(route('public.checkout.success', session('tracked_start_checkout')))
                    ->setMessage(__('Checkout successfully!'));
            } else {
                $this->storeLocalPayment([
                    'amount'          => $responsePay->amount,
                    'charge_id'       => $transId,
                    'currency'        => 'VND',
                    'payment_channel' => MOMOPAY_PAYMENT_METHOD_NAME,
                    'status'          => PaymentStatusEnum::FAILED,
                    'customer_id'     => auth('customer')->check() ? auth('customer')->user()
                        ->getAuthIdentifier() : null,
                    'payment_type'    => $responsePay->payType,
                    'order_id'        => $orderId,
                ]);

                OrderHelper::processOrder($orderId, $transId);

                return $response
                    ->setError()
                    ->setNextUrl(route('public.checkout.success', session('tracked_start_checkout')))
                    ->setMessage($responsePay->localMessage);
            }
        } catch (Exception $e) {
            Log::error('checkStatusPaymentMomo:' . $e->getMessage());
            abort(500);
        }
    }
}
