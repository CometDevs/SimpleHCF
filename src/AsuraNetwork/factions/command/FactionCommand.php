<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\command;

use AsuraNetwork\factions\command\subcommands\CreateSubCommand;
use AsuraNetwork\factions\command\subcommands\HelpSubCommand;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FactionCommand extends BaseCommand{

    protected function prepare(): void{
        $this->setUsage(TextFormat::GREEN . "/faction help");
        $this->registerSubCommand(new HelpSubCommand("help", "Factions commands"));
        $this->registerSubCommand(new CreateSubCommand("create", "Create your own faction"));
    }


    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        $sender->sendMessage(TextFormat::RED . $this->getUsage());
    }
}