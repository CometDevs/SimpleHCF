<?php

namespace AsuraNetwork\session\modules;

use AsuraNetwork\factions\claim\Claim;
use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\utils\FactionConfig;
use pocketmine\world\Position;

class ClaimSelectionModule extends Module{

    protected bool $enabled = false;
    protected ?Position $pos1 = null;
    protected ?Position $pos2 = null;

    public function getId(): string{
        return ModuleIds::CLAIM_SELECTION;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void{
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool{
        return $this->enabled;
    }

    /**
     * @return Position|null
     */
    public function getPos1(): ?Position{
        return $this->pos1;
    }

    /**
     * @return Position|null
     */
    public function getPos2(): ?Position{
        return $this->pos2;
    }

    /**
     * @param Position|null $pos1
     */
    public function setPos1(?Position $pos1): void{
        $this->pos1 = $pos1;
    }

    /**
     * @param Position|null $pos2
     */
    public function setPos2(?Position $pos2): void{
        $this->pos2 = $pos2;
    }

    public function getPrice(Claim $claim, ?Faction $faction = null, bool $buying = true): int{
        $x = abs($this->getPos1()->x - $this->getPos2()->x);
        $z = abs($this->getPos1()->z - $this->getPos2()->z);
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
        if ($buying && $faction != null) {
            $curPrice += 500;
        }

        return ((int) $curPrice);
    }
}