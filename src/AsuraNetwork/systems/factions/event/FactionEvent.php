<?php

namespace AsuraNetwork\systems\factions\event;

use AsuraNetwork\systems\factions\Faction;
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