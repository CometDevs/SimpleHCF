<?php

namespace AsuraNetwork\factions\utils;

use AsuraNetwork\Loader;

final class FactionConfig{

    public static function getMaxName(): int{
        return Loader::$factionConfig['configuration']['name']['length']['maximum'];
    }

    public static function getMinName(): int{
        return Loader::$factionConfig['configuration']['name']['length']['minimum'];
    }

}