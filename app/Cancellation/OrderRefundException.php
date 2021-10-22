<?php

namespace App\Cancellation;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class OrderRefundException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
