<?php

declare(strict_types=1);

namespace AsuraNetwork\session;

use AsuraNetwork\factions\Faction;
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
