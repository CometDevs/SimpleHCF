<?php

namespace AsuraNetwork\factions\command\subcommands;

use AsuraNetwork\factions\command\arguments\FactionArgument;
use AsuraNetwork\factions\Faction;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InfoSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new FactionArgument('faction'));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        $faction = $args['faction'];
        if ($sender instanceof Player && $faction instanceof Faction){
            $faction->sendTeamInfo($sender);
        }
    }
}