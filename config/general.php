<?php

return [
    'domain_api_sandbox' => env(
            'MOMO_DOMAIN_API_SANDBOX',
            'https://test-payment.momo.vn'
        ) . '/gw_payment/transactionProcessor',

    'domain_api_product' => env(
            'MOMO_DOMAIN_API_PRODUCT',
            'https://payment.momo.vn'
        ) . '/gw_payment/transactionProcessor',
];
