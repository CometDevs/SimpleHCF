<?php
namespace AsuraNetwork\session;

use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

/**
 * Class SessionFactory
 * @package AsuraNetwork\session
 */
class SessionFactory {
    use SingletonTrait;

    /** @var Session[] */
    private array $sessions = [];

    public function init(): void{
        foreach (glob(Loader::getInstance()->getDataFolder() . "players/"."*.yml") as $file) {
            $this->createSession(basename($file, ".yml"));
        }
        Loader::getInstance()->getLogger()->info(TextFormat::YELLOW . "All sessions have been loaded, number of sessions loaded: " . count($this->getSessions()));
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

    /**
     * @return Session[]
     */
    public function getSessions(): array{
        return $this->sessions;
    }
}
