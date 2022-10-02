@if (get_payment_setting('status', MOMOPAY_PAYMENT_METHOD_NAME) == 1)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}"
               value="{{ MOMOPAY_PAYMENT_METHOD_NAME }}" data-bs-toggle="collapse" data-bs-target=".payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_wrap"
               data-parent=".list_payment_method"
               @if (setting('default_payment_method') == MOMOPAY_PAYMENT_METHOD_NAME) checked @endif
        >
        <label for="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}">{{ get_payment_setting('name', MOMOPAY_PAYMENT_METHOD_NAME) }}</label>
        <div class="payment_{{ MOMOPAY_PAYMENT_METHOD_NAME }}_wrap payment_collapse_wrap collapse @if (setting('default_payment_method') == MOMOPAY_PAYMENT_METHOD_NAME) show @endif">
            <p>{!! get_payment_setting('description', MOMOPAY_PAYMENT_METHOD_NAME, __('Payment with Momopay')) !!}</p>
        </div>
    </li>
@endif
