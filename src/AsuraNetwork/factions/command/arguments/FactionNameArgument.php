<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\command\arguments;

use AsuraNetwork\factions\utils\FactionConfig;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;

final class FactionNameArgument extends RawStringArgument {

    public function getTypeName(): string {
        return "faction";
    }

    public function canParse(string $testString, CommandSender $sender): bool{
        return ctype_alnum($testString) && strlen($testString) <= FactionConfig::getMaxName() && strlen($testString) >= FactionConfig::getMinName();
    }

    public function parse(string $argument, CommandSender $sender): string{
        return $argument;
    }
}