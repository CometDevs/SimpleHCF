<?php

namespace AsuraNetwork\factions\command\arguments;

use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class PlayerArgument extends BaseArgument{

    public function __construct(string $name, bool $optional = false){
        parent::__construct($name, $optional);
    }

    public function getNetworkType():int{
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $testString, CommandSender $sender):bool{
        return ($this->parse($testString, $sender) instanceof Player);
    }

    public function parse(string $argument, CommandSender $sender):?Player{
        return $sender->getServer()->getPlayerByPrefix($argument);
    }

    public function getTypeName():string{
        return "player";
    }
}