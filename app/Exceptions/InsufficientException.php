<?php

namespace App\Exceptions;

use Exception;

class InsufficientException extends Exception
{
    public function __construct()
    {
        parent::__construct('Insufficient balance.');
    }
}
