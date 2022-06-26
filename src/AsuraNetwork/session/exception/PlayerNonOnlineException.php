<?php

namespace AsuraNetwork\session\exception;

use Exception;
use Throwable;

class PlayerNonOnlineException extends Exception{

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null){
        parent::__construct("Player $message is not online!", $code, $previous);
    }
}