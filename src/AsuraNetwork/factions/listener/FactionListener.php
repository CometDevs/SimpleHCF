<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\listener;

use AsuraNetwork\factions\event\FactionCreateEvent;
use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\Loader;
use pocketmine\event\Listener;
use pocketmine\Server;

class FactionListener implements Listener{

    public function onCreation(FactionCreateEvent $event): void{
        if (Loader::$factionConfig['broadcast']['creation'] === true){
            Server::getInstance()->broadcastMessage(LanguageFactory::getInstance()->getTranslation("faction-creation-broadcast", [$event->getFaction()->getSimplyName(), $event->getFaction()->getLeader()->getName()]));
        }
    }

    public function onDeletion(FactionDeleteEvent $event): void{
        if (Loader::$factionConfig['broadcast']['deletion'] === true){
            Server::getInstance()->broadcastMessage(LanguageFactory::getInstance()->getTranslation("faction-deletion-broadcast", [$event->getFaction()->getSimplyName(), $event->getFaction()->getLeader()->getName()]));
        }
    }

}