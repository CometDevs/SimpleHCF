<?php

declare(strict_types=1);

namespace AsuraNetwork\session\listener;

use AsuraNetwork\session\SessionFactory;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\VanillaItems;

class SessionListener implements Listener{

    public function handleJoin(PlayerJoinEvent $event): void{
        SessionFactory::getInstance()->create($event->getPlayer());
    }

    public function handleQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        SessionFactory::getInstance()->get($player->getName())?->onDisconnect();
    }

}