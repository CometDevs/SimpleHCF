<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\command\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class HelpSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->registerArgument(0, new IntegerArgument("page", true));
        // TODO: Implement prepare() method.
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        // TODO: Implement onRun() method.
    }
}