<?php

namespace AsuraNetwork\modules\claim;

use AsuraNetwork\factions\Faction;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;

class ClaimSelectionModule{
    use SingletonTrait;

    /** @var ClaimSelectionHistory[] */
    protected array $claimers = [];

    /**
     * @return ClaimSelectionHistory[]
     */
    public function getClaimers(): array{
        return $this->claimers;
    }

    public function add(Player $player, Faction $faction, string $claim_type, bool $buying = true): void{
        if ($this->has($player)) return;
        $this->claimers[$player->getName()] = new ClaimSelectionHistory($player, $faction, $claim_type, $buying);
    }

    public function has(Player $player): bool{
        return isset($this->claimers[$player->getName()]);
    }

    public function checkPlayer(Player $player, Position $position, int $corner = 1): void{
        if ($this->has($player)){
            if ($corner === 1){
                $this->claimers[$player->getName()]->setCorner1($position);
            }elseif ($corner === 2){
                $this->claimers[$player->getName()]->setCorner2($position);
            } elseif($corner === 3){
                $this->claimers[$player->getName()]->accept();
            }
        }
    }

    public function close(Player $player): void{

    }
}