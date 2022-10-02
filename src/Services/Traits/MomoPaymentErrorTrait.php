<?php

namespace TTSoft\MomoPay\Services\Traits;

use Exception;
use Log;

trait MomoPaymentErrorTrait
{
    /**
     * @var string
     */
    protected $errorMessage = null;

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
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
     */
    protected function setErrorMessageAndLogging($exception)
    {
        try {
            $this->errorMessage = $exception->getMessage();

            Log::error('Failed to make a payment charge.');
        } catch (Exception $exception) {
            Log::error(
                'Failed to make a payment charge.',
                $exception->getMessage()
            );
        }
    }
}
