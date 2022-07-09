<?php

namespace App\Exceptions;

use Exception;

class PaymentServiceInavailableException extends Exception
{
    public function __construct()
    {
        parent::__construct('Payment service inavailable.');
    }
}
