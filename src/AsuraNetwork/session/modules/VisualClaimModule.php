<?php

namespace AsuraNetwork\session\modules;

use pocketmine\block\BlockLegacyIds;

class VisualClaimModule extends Module{

    public static array $claim_blocks = [
        BlockLegacyIds::DIAMOND_BLOCK, BlockLegacyIds::GOLD_BLOCK, BlockLegacyIds::LOG,
        BlockLegacyIds::BRICK_BLOCK, BlockLegacyIds::WOOD,
        BlockLegacyIds::REDSTONE_BLOCK, BlockLegacyIds::LAPIS_BLOCK, BlockLegacyIds::CHEST,
        BlockLegacyIds::MELON_BLOCK, BlockLegacyIds::STONE, BlockLegacyIds::COBBLESTONE,
        BlockLegacyIds::COAL_BLOCK, BlockLegacyIds::DIAMOND_ORE, BlockLegacyIds::COAL_ORE,
        BlockLegacyIds::GOLD_ORE, BlockLegacyIds::REDSTONE_ORE, BlockLegacyIds::FURNACE
    ];

    public function getId(): string{
        return ModuleIds::CLAIM_VISUAL;
    }


}