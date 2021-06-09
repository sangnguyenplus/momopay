<?php

namespace TTSoft\MomoPay\Services\Traits;

use Exception;
use Log;
use Stripe\Exception\ApiErrorException;

trait MomoPaymentErrorTrait
{
    /**
     * @var string
     */
    protected $errorMessage = null;

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param null $message
     */
    public function setErrorMessage($message = null)
    {
        $this->errorMessage = $message;
    }

    /**
     * Set error message and logging that error
     *
     * @param Exception $exception
     * @param integer $case
     */
    protected function setErrorMessageAndLogging($exception)
    {
        try {
            if (!$exception instanceof ApiErrorException) {
                $this->errorMessage = $exception->getMessage();
            } else {
                $body = $exception->getJsonBody();
                $error = $body['error'];
                if (!empty($err['message'])) {
                    $this->errorMessage = $error['message'];
                } else {
                    $this->errorMessage = $exception->getMessage();
                }
            }

            Log::error('Failed to make a payment charge.');
        } catch (Exception $exception) {
            Log::error(
                'Failed to make a payment charge.',
                $exception->getMessage()
            );
        }
    }
}
