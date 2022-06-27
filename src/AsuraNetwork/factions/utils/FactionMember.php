<?php

declare(strict_types=1);

namespace AsuraNetwork\factions\utils;

use AsuraNetwork\factions\Faction;

class FactionMember{

    protected array $data;
    private string $name;
    private Faction $faction;
    private FactionRole $factionRole;

    /**
     * @param string $name
     * @param Faction $faction
     * @param FactionRole $factionRole
     * @param array $data
     */
    public function __construct(string $name, Faction $faction, FactionRole $factionRole, array $data){
        $this->name = $name;
        $this->faction = $faction;
        $this->factionRole = $factionRole;
        $this->data = $data;
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

    public function getKills(): int{
        return $this->data['kills'] ?? 0;
    }

    public function getDeaths(): int{
        return $this->data['deaths'];
    }

    public function getJoinTime(): string{
        return $this->data['join-time'];
    }

    public function getInvited(): string{
        return $this->data['invited-by'];
    }

}