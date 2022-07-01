<?php

namespace AsuraNetwork\modules\claim;

use AsuraNetwork\factions\claim\Claim;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class ViewClaimModule{
    use SingletonTrait;

    /** @var ViewHistory[] */
    private array $viewers = [];

    /**
     * @return ViewHistory[]
     */
    public function getViewers(): array{
        return $this->viewers;
    }

    public function add(Player $player): void{
        $this->viewers[$player->getName()] = new ViewHistory($player);
    }

    public function has(Player $player): bool{
        return isset($this->viewers[$player->getName()]);
    }

    public function close(Player $player): void{
        if ($this->has($player)){
            $this->viewers[$player->getName()]->getOldClaim()?->viewColumn($player, true);
            $this->viewers[$player->getName()]->getClaim()?->viewColumn($player, true);
            $this->viewers[$player->getName()]->setClaim(null);
            $this->viewers[$player->getName()]->setOldClaim(null);
            unset($this->viewers[$player->getName()]);
        }
    }

    public function checkClaim(Player $player): void{
        if ($this->has($player)){
            $this->viewers[$player->getName()]->getOldClaim()?->viewColumn($player, true);
            $this->viewers[$player->getName()]->getClaim()?->viewColumn($player);
        }
    }

    public function moveTo(Player $player, Claim $claim): void{
        if ($this->has($player)){
            $this->viewers[$player->getName()]->setClaim($claim);
            $this->checkClaim($player);
        }
    }

}