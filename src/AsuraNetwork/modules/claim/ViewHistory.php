<?php

namespace AsuraNetwork\modules\claim;

use AsuraNetwork\factions\claim\Claim;
use pocketmine\player\Player;

class ViewHistory{

    protected Player $player;
    protected Claim|null $oldClaim = null;
    protected Claim|null $claim = null;

    /**
     * @param Player $player
     */
    public function __construct(Player $player){
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }

    /**
     * @return Claim|null
     */
    public function getClaim(): ?Claim{
        return $this->claim;
    }

    /**
     * @return Claim|null
     */
    public function getOldClaim(): ?Claim{
        return $this->oldClaim;
    }

    /**
     * @param Claim|null $claim
     */
    public function setClaim(?Claim $claim): void{
        $this->setOldClaim($this->claim);
        $this->claim = $claim;
    }

    /**
     * @param Claim|null $oldClaim
     */
    public function setOldClaim(?Claim $oldClaim): void{
        $this->oldClaim = $oldClaim;
    }

}