<?php

declare(strict_types=1);

namespace AsuraNetwork\session;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\session\exception\PlayerNonOnlineException;
use pocketmine\player\Player;
use pocketmine\Server;

/**
 * Class Session
 * @package AsuraNetwork\session
 */
class Session{

    /** @var Faction|null */
    private ?Faction $faction = null;
    private ?FactionRole $role = null;
    private string $name;
    private array $data;

    /**
     * Session constructor.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name, array $data) {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData(): array{
        return $this->data;
    }

    /**
     * @return Faction|null
     */
    public function getFaction(): ?Faction{
        return $this->faction;
    }

    /**
     * @return FactionRole|null
     */
    public function getRole(): ?FactionRole{
        return $this->role;
    }

    public function hasFaction(): bool{
        return $this->faction !== null;
    }

    /**
     * @param Faction|null $faction
     */
    public function setFaction(?Faction $faction): void{
        $this->faction = $faction;
    }

    /**
     * @param FactionRole|null $role
     */
    public function setRole(?FactionRole $role): void{
        $this->role = $role;
    }

    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player {
        try {
            return $this->getPlayerNonNull();
        } catch (PlayerNonOnlineException $exception) {
            return null;
        }
    }

    /**
     * @return Player
     * @throws PlayerNonOnlineException
     */
    public function getPlayerNonNull(): Player{
        $player = Server::getInstance()->getPlayerExact($this->name);
        if ($player === null){
            throw new PlayerNonOnlineException($this->name);
        }
        return $player;
    }
}
