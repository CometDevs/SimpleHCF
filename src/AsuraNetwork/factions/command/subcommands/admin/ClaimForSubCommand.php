<?php

namespace AsuraNetwork\factions\command\subcommands\admin;

use AsuraNetwork\factions\command\arguments\FactionArgument;
use AsuraNetwork\factions\Faction;
use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\modules\claim\ClaimSelectionModule;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ClaimForSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new FactionArgument('faction'));
        //$this->setPermission("claimfor.subcommand");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if (!$sender instanceof Player) return;
        $faction = $args['faction'];
        if ($faction instanceof Faction){
            if (!ClaimSelectionModule::getInstance()->has($sender)) {
                ClaimSelectionModule::getInstance()->add($sender, false);
                $sender->sendMessage(LanguageFactory::getInstance()->getTranslation('claiming-for', [$faction->getSimplyName()]));
            } else {
                $sender->sendMessage(LanguageFactory::getInstance()->getTranslation('already-claiming'));
            }
        }
    }
}