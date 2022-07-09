<?php

namespace App\Exceptions;

use Exception;

class InvalidTransferException extends Exception
{
    public function __construct()
    {
        parent::__construct('Logists cannot send the value.');
    }
}
