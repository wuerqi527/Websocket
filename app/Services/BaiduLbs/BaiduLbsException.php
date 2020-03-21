<?php

namespace App\Services\BaiduLbs;

use LogicException;;

class BaiduLbsException extends LogicException
{
    public function __construct($message)
    {
        if (is_null($message)) {
            return;
        }

        $this->message = $message;
    }
}
