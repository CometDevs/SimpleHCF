<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\command\subcommands;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\Loader;
use AsuraNetwork\session\SessionFactory;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new RawStringArgument("faction_name"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if(!$sender instanceof Player) return;
        $faction_name = $args["faction_name"];
        if (SessionFactory::getInstance()->get($sender->getName())?->hasFaction() === true){
            $sender->sendMessage(LanguageFactory::getInstance()->getTranslation("already-in-a-faction"));
            return;
        }
        if (FactionsFactory::getInstance()->exists($faction_name)){
            $sender->sendMessage(LanguageFactory::getInstance()->getTranslation("faction-exists-command"));
            return;
        }
        if (in_array($faction_name, Loader::$factionConfig['configuration']['name']['banned'])){
            $sender->sendMessage(LanguageFactory::getInstance()->getTranslation("faction-name-banned"));
            return;
        }
        if (Loader::$factionConfig['configuration']['reduce-balance-on-creation'] === true) {
            $error = false;
            EconomyFactory::getInstance()->getProvider()->getMoney($sender, function (int|float $amount) use(&$error): void{
                if($amount < Loader::$factionConfig['configuration']["reduce-balance"]){
                    $error = true;
                }
            });
            if ($error){
                $sender->sendMessage(LanguageFactory::getInstance()->getTranslation("insufficient-funds"));
                return;
            }
        }
        $faction = FactionsFactory::getInstance()->create([
            "name" => $faction_name,
            "balance" => Loader::$factionConfig['configuration']['start-balance'],
            "home" => null,
            "claim" => null,
            "kills" => 0,
            "dtr" => 1.1,
            "points" => 0,
            "koth-capped" => 0,
            "members" => [
                $sender->getName() => [
                    "role" => FactionRole::LEADER()->name(),
                    "kills" => 0,
                    "deaths" => 0,
                    "join-time" => date('Y-m-d H:i:s'),
                    "invited-by" => "none"
                ]
            ]
        ]);
        if ($faction !== null) {
            if (Loader::$factionConfig['configuration']['reduce-balance-on-creation'] === true) {
                EconomyFactory::getInstance()->getProvider()->takeMoney($sender, Loader::$factionConfig['configuration']["reduce-balance"]);
            }
            SessionFactory::getInstance()->get($sender->getName())?->setFaction($faction);
        }
    }
}