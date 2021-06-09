@php $momopayStatus = get_payment_setting('status', MOMOPAY_PAYMENT_METHOD_NAME); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img src="{{ url('vendor/core/plugins/momopay/images/logo-momo.jpg') }}" alt="Momo pay" height="40">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://momo.vn" target="_blank">{{ __('Momopay') }}</a>
                    <p>{{ __('Customer can buy product and pay via Momopay') }}</p>
                </li>
            </ul>
        </td>
    </tr>
    </tbody>
    <tbody class="border-none-t">
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-left" style="margin-top: 5px;">
                <div
                    class="payment-name-label-group @if (get_payment_setting('status', MOMOPAY_PAYMENT_METHOD_NAME) == 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span> <label
                        class="ws-nm inline-display method-name-label">{{ get_payment_setting('name', MOMOPAY_PAYMENT_METHOD_NAME) }}</label>
                </div>
            </div>
            <div class="float-right">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($momopayStatus == 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($momopayStatus == 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="paypal-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', MOMOPAY_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'Momopay']) }}</label>
                        </li>
                        <li class="payment-note">
                            <p>{{ trans('plugins/payment::payment.configuration_requirement', ['name' => 'Momopay']) }}
                                :</p>
                            <ul class="m-md-l" style="list-style-type:decimal">
                                <li style="list-style-type:decimal">
                                    <a href="https://business.momo.vn/" target="_blank">
                                        {{ __('Register an account on Momo') }}
                                    </a>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ __('After registration at :name, you will have Partner Code, Access Key, Secret Key, Public Key', ['name' => 'Momopay']) }}</p>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ __('Enter Partner Code, Access Key, Secret Key, Public Key into the box in right hand') }}</p>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group">
                            <label class="text-title-field"
                                   for="momopay_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input" name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_name"
                                   id="momopay_name" data-counter="400"
                                   value="{{ get_payment_setting('name', MOMOPAY_PAYMENT_METHOD_NAME, __('Online payment via Momopay')) }}">
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a target="_blank"
                                                                                                  href="https://business.momo.vn/">Momopay</a>:
                        </p>
                        <div class="form-group">
                            <label class="text-title-field" for="momopay_partner_code">{{ __('Partner Code') }}</label>
                            <input type="text" class="next-input"
                                   name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_partner_code" id="momopay_partner_code"
                                   value="{{ get_payment_setting('partner_code', MOMOPAY_PAYMENT_METHOD_NAME) }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="momopay_access_key">{{ __('Access Key') }}</label>
                            <input type="password" class="next-input" placeholder="••••••••" id="momopay_access_key"
                                   name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_access_key"
                                   value="{{ get_payment_setting('access_key', MOMOPAY_PAYMENT_METHOD_NAME) }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="momopay_secret_key">{{ __('Secret Key') }}</label>
                            <input type="password" class="next-input" placeholder="••••••••" id="momopay_secret_key"
                                   name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_secret_key"
                                   value="{{ get_payment_setting('secret_key', MOMOPAY_PAYMENT_METHOD_NAME) }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="momopay_public_key">{{ __('Public Key') }}</label>
                            <input type="password" class="next-input" placeholder="••••••••" id="momopay_public_key"
                                   name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_public_key"
                                   value="{{ get_payment_setting('public_key', MOMOPAY_PAYMENT_METHOD_NAME) }}">
                        </div>
                        {!! Form::hidden('payment_'.MOMOPAY_PAYMENT_METHOD_NAME.'_mode', 1) !!}
                        <div class="form-group">
                            <label class="next-label">
                                <input type="checkbox" class="hrv-checkbox" value="0" name="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_mode"
                                 @if (get_payment_setting('mode', MOMOPAY_PAYMENT_METHOD_NAME) == 0) checked @endif>
                                {{ trans('plugins/payment::payment.sandbox_mode') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-right">
                <button class="btn btn-warning disable-payment-item @if ($momopayStatus == 0) hidden @endif"
                        type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-save @if ($momopayStatus == 1) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-update @if ($momopayStatus == 0) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>
