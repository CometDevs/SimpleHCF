<?php

namespace AsuraNetwork\factions\command\subcommands\leader;

use AsuraNetwork\factions\command\arguments\PlayerArgument;
use AsuraNetwork\factions\command\constraints\RequiredRoleConstraint;
use AsuraNetwork\factions\utils\FactionRole;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InviteSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new RequiredRoleConstraint($this, [FactionRole::COLEADER(), FactionRole::LEADER()]));
        $this->registerArgument(0, new PlayerArgument("player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if (!$sender instanceof Player) return;

    }
}