<?php
namespace AsuraNetwork\session;

use AsuraNetwork\Loader;
use pocketmine\player\Player;
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
        if (!is_dir(Loader::getInstance()->getDataFolder() . "players")) @mkdir(Loader::getInstance()->getDataFolder() . "players");
        foreach (glob(Loader::getInstance()->getDataFolder() . "players/"."*.yml") as $file) {
            $this->add(new Session(basename($file, ".yml"), yaml_parse_file($file)));
        }
        Loader::getInstance()->getLogger()->info(TextFormat::YELLOW . "All sessions have been loaded, number of sessions loaded: " . count($this->getSessions()));
    }

    /**
     * @param string $name
     * @return Session|null
     */
    public function get(string $name): ?Session {
        return $this->sessions[$name] ?? null;
    }

    /**
     * @param Session $session
     * @return void
     */
    public function add(Session $session): void {
        $this->sessions[$session->getName()] = $session;
    }

    public function exists(string $name): bool{
        return isset($this->sessions[$name]);
    }

    public function create(Player $player): void{
        if ($this->exists($player->getName())){
            return;
        }
        $this->add(new Session($player->getName(), [
            "faction" => null,
            "faction-role" => null,
            "invincible-time" => 3600,
            "kills" => 0,
            "deaths" => 0,
            "cooldowns" => []
        ]));
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array{
        return $this->sessions;
    }
}
