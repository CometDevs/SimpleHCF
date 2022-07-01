<?php

namespace AsuraNetwork\factions\listener;

use AsuraNetwork\modules\claim\ClaimSelectionModule;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\nbt\tag\IntTag;
use pocketmine\world\Position;

class ClaimListener implements Listener{

    public function handleInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $corner = $event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK ? 1 : 2;
        $item = $event->getItem();
        if (ClaimSelectionModule::getInstance()->has($player) && $item->getNamedTag()->getTag('claim_axe') instanceof IntTag){
            ClaimSelectionModule::getInstance()->checkPlayer($player, $event->getBlock()->getPosition(), $corner);
        }
    }

    public function handleAcceptClaim(PlayerItemUseEvent $event): void{
        $item = $event->getItem();
        $player = $event->getPlayer();
        if (ClaimSelectionModule::getInstance()->has($player) && $item->getNamedTag()->getTag('claim_axe') instanceof IntTag){
            ClaimSelectionModule::getInstance()->checkPlayer($player, $player->getPosition(), 3);
        }
    }

}