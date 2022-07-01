<?php

namespace AsuraNetwork\factions\command\subcommands;

use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\modules\claim\ViewClaimModule;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MapSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if (!$sender instanceof Player) return;
        if (ViewClaimModule::getInstance()->has($sender)){
            ViewClaimModule::getInstance()->close($sender);
            $sender->sendMessage(LanguageFactory::getInstance()->getTranslation('claim-map-disabled'));
        } else {
            ViewClaimModule::getInstance()->add($sender);
            $sender->sendMessage(LanguageFactory::getInstance()->getTranslation('claim-map-enabled'));
        }
    }
}