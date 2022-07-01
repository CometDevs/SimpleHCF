<?php

namespace AsuraNetwork\factions\claim;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\utils\VectorUtils;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

abstract class Claim{

    protected Faction $faction;
    private Position $pos1;
    private Position $pos2;

    public function __construct(Faction $faction, array $positions){
        $this->faction = $faction;
        $this->pos1 = VectorUtils::strToPosition($positions['pos1']);
        $this->pos2 = VectorUtils::strToPosition($positions['pos2']);
    }

    public function getPos1(): Position{
        return $this->pos1;
    }

    public function getPos2(): Position{
        return $this->pos2;
    }

    public function viewColumn(Player $player, bool $hide = false): void{
        $blocks = !$hide ? [VanillaBlocks::GLASS(), VanillaBlocks::DIAMOND()] : [VanillaBlocks::AIR()];
        $pos1 = new Vector3($this->getPos1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getPos1()->getFloorZ());
        $pos2 = new Vector3($this->getPos2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getPos2()->getFloorZ());
        $pos3 = new Vector3($this->getPos1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getPos2()->getFloorZ());
        $pos4 = new Vector3($this->getPos2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getPos1()->getFloorZ());
        for($i = ($player->getPosition()->getFloorY()-10); $i < ($player->getPosition()->getFloorY() + 40); $i++){
            $player->getNetworkSession()->sendDataPacket($this->createPacket($blocks[array_rand($blocks)], $i, $pos1));
            $player->getNetworkSession()->sendDataPacket($this->createPacket($blocks[array_rand($blocks)], $i, $pos2));
            $player->getNetworkSession()->sendDataPacket($this->createPacket($blocks[array_rand($blocks)], $i, $pos3));
            $player->getNetworkSession()->sendDataPacket($this->createPacket($blocks[array_rand($blocks)], $i, $pos4));
        }
    }

    private function createPacket(Block $block, int $y, Vector3 $v): UpdateBlockPacket{
        $pos = new BlockPosition($v->getFloorX(), $y, $v->getFloorZ());
        $b = RuntimeBlockMapping::getInstance()->toRuntimeId($block->getFullId());
        return UpdateBlockPacket::create($pos, $b, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
    }

}