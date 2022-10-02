<?php

namespace TTSoft\MomoPay\Providers;

use Botble\Payment\Enums\PaymentMethodEnum;
use Html;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Throwable;
use TTSoft\MomoPay\Services\Gateways\MomoPaymentService;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerMomopayMethod'], 126, 2);
        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithMomopay'], 126, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 126);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['MOMOPAY'] = MOMOPAY_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 126, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MOMOPAY_PAYMENT_METHOD_NAME) {
                $value = 'Momopay';
            }

            return $value;
        }, 126, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == MOMOPAY_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 126, 2);
    }

    /**
     * @param string|null $settings
     * @return string
     * @throws Throwable
     */
    public function addPaymentSettings(?string $settings): string
    {
        return $settings . view('plugins/momopay::settings')->render();
    }

    /**
     * @param string $html
     * @param array $data
     * @return string
     */
    public function registerMomopayMethod($html, array $data): string
    {
        return $html . view('plugins/momopay::methods', $data)->render();
    }

    /**
     * @param array $data
     * @param Request $request
     * @return array
     * @throws BindingResolutionException
     */
    public function checkoutWithMomopay(array $data, Request $request): array
    {
        if ($request->input('payment_method') == MOMOPAY_PAYMENT_METHOD_NAME) {
            $momoPaymentService = $this->app->make(MomoPaymentService::class);

            $checkoutUrl = $momoPaymentService->execute($request);

            if ($checkoutUrl) {
                $data['checkoutUrl'] = $checkoutUrl;
            } else {
                $data['error'] = true;
                $data['message'] = $momoPaymentService->getErrorMessage();
            }
        }

        return $data;
    }
}
