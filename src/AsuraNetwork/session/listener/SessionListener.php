<?php

namespace AsuraNetwork\session\listener;

use AsuraNetwork\session\SessionFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class SessionListener implements Listener{

    public function handleJoin(PlayerJoinEvent $event): void{
        SessionFactory::getInstance()->create($event->getPlayer());
    }

}