<?php

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\factions\utils\FactionMember;
use AsuraNetwork\factions\utils\FactionRole;

class Faction{

    /** @var FactionMember[] */
    private array $members = [];

    public function __construct(
        private FactionData $factionData
    ){
        $this->initPlayers();
    }

    public function initPlayers(): void{
        foreach ($this->factionData->getData()['members'] as $member => $data) {
            if (FactionRole::fromString($data['role']) == null){
                continue;
            }
            $this->members[$member] = new FactionMember($member, $this, FactionRole::fromString($data['role']));
        }
    }

    /**
     * @return FactionMember[]
     */
    public function getMembers(): array{
        return $this->members;
    }

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

    public function delete(): void{
        (new FactionDeleteEvent($this))->call();
    }

}