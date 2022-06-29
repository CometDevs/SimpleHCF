<?php

namespace AsuraNetwork\factions\command\arguments;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\FactionsFactory;
use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class FactionArgument extends BaseArgument{

    public function getNetworkType():int{
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $testString, CommandSender $sender): bool{
        return ($this->parse($testString, $sender) instanceof Faction);
    }

    public function parse(string $argument, CommandSender $sender): ?Faction{
        return FactionsFactory::getInstance()->getFactionByPrefix($argument);
    }

    public function getTypeName(): string{
        return "faction";
    }
}