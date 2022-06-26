<?php

namespace AsuraNetwork\systems\factions;

use AsuraNetwork\systems\factions\utils\FactionData;

class Faction{

    public function __construct(
        private FactionData $factionData
    ){}

    /**
     * @return FactionData
     */
    public function getFactionData(): FactionData{
        return $this->factionData;
    }

    public function getName(): string{
        return $this->getFactionData()->getSimple("name");
    }

    public function getBalance(): int{
        return $this->getFactionData()->getSimple("balance", 1000);
    }

}