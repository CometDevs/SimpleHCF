<?php

declare(strict_types=1);

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\factions\event\player\PlayerJoinFactionEvent;
use AsuraNetwork\factions\modules\AmountModule;
use AsuraNetwork\factions\modules\ModulesTrait;
use AsuraNetwork\factions\threads\ThreadManager;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\factions\utils\FactionMember;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\Loader;
use AsuraNetwork\session\Session;
use AsuraNetwork\session\SessionFactory;
use RuntimeException;

class Faction{
    use ModulesTrait;

    public const BALANCE_MODULE = "balance";
    public const DTR_MODULE = "dtr";
    public const POINTS_MODULE = "points";
    public const KILLS_MODULE = "kills";
    public const KOTH_CAPPED_MODULE = "koth-capped";

    /** @var FactionMember[] */
    private array $members = [];

    /** @var AmountModule[] */
    private array $modules = [];

    public function __construct(
        private FactionData $factionData){
        $this->initModules();
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
     * @return AmountModule[]
     */
    public function getModules(): array{
        return $this->modules;
    }

    /**
     * @param string $name
     * @return FactionMember|null
     */
    public function getMember(string $name): ?FactionMember{
        return $this->members[$name] ?? null;
    }

    public function addMember(Session $member, string $inviter = "none"): void{
        if ($this->getMember($member->getName()) === null){
            return;
        }
        $ev = new PlayerJoinFactionEvent($this, $member->getPlayerNonNull());
        $ev->call();
        if ($ev->isCancelled()){
            return;
        }
        $this->members[$member->getName()] = new FactionMember($member->getName(), $this, FactionRole::MEMBER(),[
            "kills" => 0,
            "deaths" => 0,
            "join-time" => date('Y-m-d H:i:s'),
            "invited-by" => $inviter
        ]);
        $member->setFaction($this);
        $member->setRole(FactionRole::MEMBER());
        $this->senTranslation("player-joined", [$member->getName(), $inviter]);
        $this->log("Player {$member->getName()} has joined the faction and was invited by $inviter");
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

    public function getLeader(): FactionMember{
        foreach ($this->members as $member) {
            if ($member->getFactionRole()->equals(FactionRole::LEADER())){
                return $member;
            }
        }
        throw new RuntimeException("Leader not found!");
    }

    public function delete(): void{
        (new FactionDeleteEvent($this))->call();
    }

    public function sendMessage(string $message): void{
        foreach ($this->members as $member) {
            SessionFactory::getInstance()->get($member->getName())?->sendMessage($message);
        }
    }

    public function senTranslation(string $message, array $params = []): void{
        foreach ($this->members as $member) {
            SessionFactory::getInstance()->get($member->getName())?->sendTranslation($message, $params);
        }
    }


    public function save(): void{
        file_put_contents(Loader::getInstance()->getDataFolder() . "factions/" . $this->getName() . ".yml", $this->getFactionData()->serialize());
    }

    public function log(string $action): void{
        ThreadManager::getInstance()->factionLog($this, $action);
    }

}