<?php

declare(strict_types=1);

namespace AsuraNetwork\session\listener;

use AsuraNetwork\session\SessionFactory;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;

class SessionListener implements Listener{

    public function handleJoin(PlayerJoinEvent $event): void{
        SessionFactory::getInstance()->create($event->getPlayer());
    }

    public function handleUseEnderPearl(PlayerItemUseEvent $event): void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->equals(VanillaItems::ENDER_PEARL())){

        }
    }

}