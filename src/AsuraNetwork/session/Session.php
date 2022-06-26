<?php
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

    /**
     * Session constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        $this->name = $name;
        $this->loadUserData();
    }

    /**
     * @return Faction|null
     */
    public function getFaction(): ?Faction{
        return $this->faction;
    }

    /**
     * @return void
     */
    public function loadUserData(): void{

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
