<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\utils;

use pocketmine\utils\EnumTrait;

/**
 * @method static FactionRole LEADER()
 * @method static FactionRole COLEADER()
 * @method static FactionRole CAPTAIN()
 * @method static FactionRole MEMBER()
 */

class FactionRole{
    use EnumTrait;

    protected static function setup(): void{
        self::registerAll(
            new FactionRole("Leader"),
            new FactionRole("CoLeader"),
            new FactionRole("Captain"),
            new FactionRole("Member")
        );
    }

    public static function fromString(string $name): ?self{
        foreach (self::getAll() as $enum) {
            if ($enum->enumName == $name){
                return $enum;
            }
        }
        return null;
    }
}