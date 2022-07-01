<?php

namespace AsuraNetwork\utils;

use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\WorldException;
use TypeError;

final class VectorUtils{

    public static function strToVector(?string $vector): Vector3{
        self::thrownOnVectorError($vector);
        $v = explode(",", $vector);
        return new Vector3($v[0], $v[1], $v[2]);
    }

    public static function strToPosition(?string $position): Position{
        self::thrownOnVectorError($position, 4, true);
        $pos = explode(",", $position);
        return new Position($pos[0], $pos[1], $pos[2], Server::getInstance()->getWorldManager()->getWorldByName($pos[3]));
    }

    public static function posToString(Position $position): string{
        return implode(",", [$position->getFloorX(), $position->getFloorY(), $position->getFloorZ(), $position->getWorld()->getFolderName()]);
    }

    private static function thrownOnVectorError(?string $vector, int $args = 3, bool $checkWorld = false): void{
        if ($vector === null) throw new TypeError("vector MUST be type string but null given!");
        $v = explode(",", $vector);
        if (count($v) < $args) throw new InvalidArgumentException("invalid vector!");
        if ($checkWorld) {
            if (!Server::getInstance()->getWorldManager()->isWorldGenerated($v[3])){
                throw new WorldException("World $v[3] not exists!");
            }
        }
    }
}