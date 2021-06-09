<?php

Route::group(['namespace' => 'TTSoft\MomoPay\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => 'payments'], function () {
        Route::get('check-payment-momo', 'MomoPaymentController@checkStatusPaymentMomo')->name('payments.momo.status');
    });
});
