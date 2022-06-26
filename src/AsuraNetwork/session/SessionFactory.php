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

    public function __construct(){
    }

    /**
     * @param string $name
     * @return Session|null
     */
    public function getSession(string $name): ?Session {
        return $this->sessions[$name] ?? null;
    }

    /**
     * @param string $name
     * @return void
     */
    public function createSession(string $name): void {
        $this->sessions[$name] = new Session($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public function deleteSession(string $name): void {
        unset($this->sessions[$name]);
    }
}
