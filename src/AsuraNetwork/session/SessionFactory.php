<?php
namespace AsuraNetwork\session;

use AsuraNetwork\Loader;
use AsuraNetwork\session\Session;
use pocketmine\player\Player;
use pocketmine\utils\Config;

/**
 * Class SessionFactory
 * @package AsuraNetwork\session
 */
class SessionFactory {
  
  /** @var Session[] $sessions */
  private array $sessions = [];
  
  /**
   * @param Player $player
   * @return Session|null
   */
  public function getSession(Player $player): ?Session {
    return $this->sessions[$player->getName()] ?? null;
  }
  
  /**
   * @param Player $player
   * @return void
   */
  public function createSession(Player $player): void {
    $this->sessions[$player->getName()] = new Session($player);
  }
  
  /**
   * @param Player $player
   * @return void
   */
  public function deleteSession(Player $player): void {
    unset($this->sessions[$player->getName()]);
  }
}
