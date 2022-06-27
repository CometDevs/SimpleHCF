<?php

namespace AsuraNetwork\session\modules;

use _PHPStan_7a922a511\React\Dns\Query\SelectiveTransportExecutor;
use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\FactionsFactory;

class InviteModule extends Module{

    protected array $invites = [];

    public function getId(): string{
        return ModuleIds::INVITE;
    }

    /**
     * @return array
     */
    public function getInvites(): array{
        return $this->invites;
    }

    public function add(Faction $faction, string $inviter): void{
        $this->invites[$faction->getName()] = $inviter;
        $this->session->sendTranslation("invited-to", [$faction->getName(), $inviter]);
    }

    public function has(string $faction_name): bool{
        return isset($this->invites[$faction_name]);
    }

    public function delete(Faction $faction): void{
        unset($this->invites[$faction->getName()]);
    }

    public function accept(string $faction_name): void{
        if (!isset($this->invites[$faction_name])){
            $this->session->sendTranslation("not-invited", [$faction_name]);
            return;
        }
        $faction = FactionsFactory::getInstance()->get($faction_name);
        if ($faction instanceof Faction) {
            $faction->addMember($this->session, $this->invites[$faction_name]);
        }
    }

}