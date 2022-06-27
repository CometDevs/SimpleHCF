<?php

declare(strict_types=1);

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\factions\utils\FactionMember;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\Loader;
use AsuraNetwork\session\Session;
use pocketmine\MemoryManager;
use pocketmine\utils\Filesystem;

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
            $this->members[$member] = new FactionMember($member, $this, FactionRole::fromString($data['role']), [
                "kills" => $data['kills'],
                "deaths" => $data['deaths'],
                "join-time" => $data['join-time'],
                "invited-by" => $data['invited-by'],
            ]);
        }
    }

    /**
     * @return FactionMember[]
     */
    public function getMembers(): array{
        return $this->members;
    }

    /**
     * @param string $name
     * @return FactionMember|null
     */
    public function getMember(string $name): ?FactionMember{
        return $this->members[$name] ?? null;
    }

    public function addMember(Session $member, string $invited = "none"): void{
        if ($this->getMember($member->getName()) === null){
            return;
        }
        $this->members[$member->getName()] = new FactionMember($member->getName(), $this, FactionRole::MEMBER(),[
            "kills" => 0,
            "deaths" => 0,
            "join-time" => date('Y-m-d H:i:s'),
            "invited-by" => $invited
        ]);
        $member->setFaction($this);
        $member->setRole(FactionRole::MEMBER());
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

    public function save(): void{
        file_put_contents(Loader::getInstance()->getDataFolder() . "factions/" . $this->getName() . ".yml", $this->getFactionData()->serialize());
    }

}