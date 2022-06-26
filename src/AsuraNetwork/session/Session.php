<?php
namespace AsuraNetwork\session;

use AsuraNetwork\systems\factions\Faction;
use pocketmine\player\Player;

/**
 * Class Session
 * @package AsuraNetwork\session
 */
class Session {
  
  /** @var Player $player */
  private Player $player;
  
  /** @var Faction $faction */
  private Faction $faction;
  
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
  public function getFaction(): ?Faction {
    return $this->faction;
  }
  
  /**
   * @return void
   */
  public function loadUserData(): void {
    
  }
  
  /**
   * @return Player
   */
  public function getPlayer(): Player {
    return $this->player;
  }
}
