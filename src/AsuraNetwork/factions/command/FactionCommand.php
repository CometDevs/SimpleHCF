<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\command;

use AsuraNetwork\factions\command\subcommands\admin\ClaimForSubCommand;
use AsuraNetwork\factions\command\subcommands\CreateSubCommand;
use AsuraNetwork\factions\command\subcommands\HelpSubCommand;
use AsuraNetwork\factions\command\subcommands\InfoSubCommand;
use AsuraNetwork\factions\command\subcommands\leader\InviteSubCommand;
use AsuraNetwork\factions\command\subcommands\MapSubCommand;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FactionCommand extends BaseCommand{

    protected function prepare(): void{
        $this->setUsage(TextFormat::GREEN . "/faction help");
        $this->registerSubCommand(new HelpSubCommand("help", "Factions commands"));
        $this->registerSubCommand(new CreateSubCommand("create", "Create your own faction"));
        $this->registerSubCommand(new InviteSubCommand("invite", "Invite a player to your faction"));
        $this->registerSubCommand(new InfoSubCommand("info", "Invite a player to your faction", ['who']));
        $this->registerSubCommand(new MapSubCommand("map", "Invite a player to your faction"));
        $this->registerSubCommand(new ClaimForSubCommand("claimfor", "Force claim for a faction", ['opclaim']));
    }


    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        // nothing ??
    }
}