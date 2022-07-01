<?php

namespace AsuraNetwork\utils;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\IntTag;

final class ItemUtils{

    public static function getClaimingAxe(): Item{
        $axe = VanillaItems::GOLDEN_AXE()
            ->setCustomName("§a§oClaiming Wand")
            ->setLore([
                "",
                "§eRight/Left Click§6 Block",
                "§b- §fSelect claim's corners",
                "",
                "§eRight Click §6Air",
                "§b- §fCancel current claim",
                "",
                "§9Crouch §eLeft Click §6Block/Air",
                "§b- §fPurchase current claim"
            ]);
        $axe->getNamedTag()->setTag("claim_axe", new IntTag(1));
        return $axe;
    }

}