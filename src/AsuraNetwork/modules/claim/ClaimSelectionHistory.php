<?php

namespace AsuraNetwork\modules\claim;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\utils\FactionConfig;
use AsuraNetwork\modules\claim\utils\ClaimTypes;
use AsuraNetwork\utils\ItemUtils;
use AsuraNetwork\utils\VectorUtils;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class ClaimSelectionHistory{

    protected Player $player;
    protected ?Position $corner_1 = null;
    protected ?Position $corner_2 = null;
    protected bool $buying;
    protected string $claim_type;
    private Faction $faction;

    public function __construct(Player $player, Faction $faction, string $claim_type, bool $buying){
        $this->player = $player;
        $this->faction = $faction;
        $this->claim_type = $claim_type;
        $this->buying = $buying;
        $player->getInventory()->addItem(ItemUtils::getClaimingAxe());
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    public function setCorner1(Position $position): void{
        if($this->corner_1 !== null) $this->viewColumn($this->corner_1, true);
        $this->corner_1 = $position;
        $this->viewColumn($position);
        $this->player->sendMessage("Haz seleccionado el corner 1 del claim en la posición: ". $position->__toString());
        if ($this->canAccept()){
            $this->player->sendMessage("Puedes aceptar el claim, ahora agachate y da click derecho en el aire");
        }
    }

    public function setCorner2(Position $position): void{
        if($this->corner_2 !== null) $this->viewColumn($this->corner_2, true);
        $this->corner_2 = $position;
        $this->viewColumn($position);
        $this->player->sendMessage("Haz seleccionado el corner 2 del claim en la posición: ". $position->__toString());
        if ($this->canAccept()){
            $this->player->sendMessage("Puedes aceptar el claim, ahora agachate y da click derecho en el aire");
        }
    }

    /**
     * @return Position|null
     */
    public function getCorner1(): ?Position{
        return $this->corner_1;
    }

    /**
     * @return Position|null
     */
    public function getCorner2(): ?Position{
        return $this->corner_2;
    }

    public function canAccept(): bool{
        if ($this->getCorner1() == null || $this->getCorner2() == null){
            return false;
        } else {
            return true;
        }
    }

    public function accept(): void{
        if ($this->canAccept()){
            $this->faction->setClaim(ClaimTypes::strToClass($this->claim_type, $this->faction, [VectorUtils::posToString($this->getCorner1()), VectorUtils::posToString($this->getCorner2())]));
        } else {
            $this->player->sendMessage("No puedes aceptar el claim necesitas pones las dos esquinas!");
        }
    }

    public function getPrice(): int{
        $x = abs($this->getCorner1()->x - $this->getCorner2()->x);
        $z = abs($this->getCorner1()->z - $this->getCorner2()->z);
        $blocks = $x * $z;
        $done = 0;
        $mod = FactionConfig::getClaimPriceByBlock();
        $curPrice = 0;

        while ($blocks > 0) {
            $blocks--;
            $done++;

            $curPrice += $mod;

            if ($done == 250) {
                $done = 0;
                $mod += 0.4;
            }
        }
        $curPrice *= 0.8;
        if ($this->buying) {
            $curPrice += 500;
        }

        return ((int) $curPrice);
    }

    public function viewColumn(Position $position, bool $hide = false): void{
        $blocks = !$hide ? [VanillaBlocks::GLASS(), VanillaBlocks::DIAMOND()] : [VanillaBlocks::AIR()];
        $pos = new Vector3($position->getFloorX(), $position->getFloorY(), $position->getFloorZ());
        for($i = $position->getFloorY(); $i < ($position->getFloorY() + 40); $i++){
            $this->getPlayer()->getNetworkSession()->sendDataPacket($this->createPacket($blocks[array_rand($blocks)], $i, $pos));
        }
    }

    private function createPacket(Block $block, int $y, Vector3 $v): UpdateBlockPacket{
        $pos = new BlockPosition($v->getFloorX(), $y, $v->getFloorZ());
        $b = RuntimeBlockMapping::getInstance()->toRuntimeId($block->getFullId());
        return UpdateBlockPacket::create($pos, $b, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
    }
}