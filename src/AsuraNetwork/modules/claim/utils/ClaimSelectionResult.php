<?php

namespace AsuraNetwork\modules\claim\utils;

final class ClaimSelectionResult{

    protected string $result;

    /**
     * @param string $result
     */
    public function __construct(string $result = "success"){
        $this->result = $result;
    }


    public static function success(): self{
        return new self();
    }

    public static function error(): self{
        return new self("error");
    }

    public static function veryBig(): self{
        return new self("big");
    }

    public static function verySmall(): self{
        return new self("small");
    }
}