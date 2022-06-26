<?php

namespace AsuraNetwork\factions\event;

use AsuraNetwork\factions\Faction;
use pocketmine\event\Event;

class FactionEvent extends Event{

    protected Faction $faction;

    public function __construct(Faction $faction){
        $this->faction = $faction;
    }

    /**
     * @return Faction
     */
    public function getFaction(): Faction{
        return $this->faction;
    }
}