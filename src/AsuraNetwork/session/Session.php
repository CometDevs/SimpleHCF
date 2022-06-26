<?php
namespace AsuraNetwork\session;

use pocketmine\player\Player;

/**
 * Class Session
 * @package AsuraNetwork\session
 */
class Session {
  
  /** @var Player $player */
  private Player $player;
  
  /**
   * Loader constructor.
   * @param Player $player
   */
  public function __construct(Player $player) {
    $this->player = $player;
  }
  
  /**
   * @return Player
   */
  public function getPlayer(): Player {
    return $this->player;
  }
}
