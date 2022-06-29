<?php

namespace AsuraNetwork\factions\utils;

use AsuraNetwork\Loader;

final class FactionConfig{

    public static function getMaxMembers(): int{
        return Loader::$factionConfig['configuration']['max-players'];
    }

    public static function getMaxDTR(): int{
        return Loader::$factionConfig['configuration']['dtr']['max'];
    }

    public static function getFreezeTime(): int{
        return Loader::$factionConfig['configuration']['dtr']['freeze-time'];
    }

    public static function getMaxName(): int{
        return Loader::$factionConfig['configuration']['name']['length']['maximum'];
    }

    public static function getMinName(): int{
        return Loader::$factionConfig['configuration']['name']['length']['minimum'];
    }

    /**
     * @return string[]
     */
    public static function getBannedNames(): array{
        return Loader::$factionConfig['configuration']['name']['banned'];
    }

    public static function getClaimMax(): int{
        return Loader::$factionConfig['configuration']['claim']['length']['maximum'];
    }

    public static function getClaimMin(): int{
        return Loader::$factionConfig['configuration']['claim']['length']['minimum'];
    }

    public static function getClaimPriceByBlock(): float{
        return Loader::$factionConfig['configuration']['claim']['price-per-block'];
    }

    public static function getStartBalance(): int{
        return Loader::$factionConfig['configuration']['start-balance'];
    }

}