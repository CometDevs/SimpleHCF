<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\utils;

use AsuraNetwork\factions\Faction;

class FactionMember{

    private string $name;
    private Faction $faction;
    private FactionRole $factionRole;

    /**
     * @param string $name
     * @param Faction $faction
     * @param FactionRole $factionRole
     */
    public function __construct(string $name, Faction $faction, FactionRole $factionRole){
        $this->name = $name;
        $this->faction = $faction;
        $this->factionRole = $factionRole;
    }

    /**
     * @return Faction
     */
    public function getFaction(): Faction{
        return $this->faction;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return FactionRole
     */
    public function getFactionRole(): FactionRole{
        return $this->factionRole;
    }

    /**
     * @param FactionRole $factionRole
     */
    public function setFactionRole(FactionRole $factionRole): void{
        $this->factionRole = $factionRole;
    }

}