<?php
namespace AsuraNetwork\session;

use AsuraNetwork\factions\Faction;
use pocketmine\player\Player;

/**
 * Class Session
 * @package AsuraNetwork\session
 */
class Session{

    /** @var Player */
    private Player $player;

    /** @var Faction|null */
    private ?Faction $faction = null;

    /**
     * Loader constructor.
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
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
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }
}
