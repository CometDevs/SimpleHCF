<?php

namespace AsuraNetwork\modules\pvp;

use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\session\SessionFactory;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class InvincibilityModule{
    use SingletonTrait;

    private array $players = [];

    /**
     * @return array
     */
    public function getPlayers(): array{
        return $this->players;
    }

    public function add(Player $player, int $time): void{
        $this->players[$player->getName()] = $time;
    }

    public function has(Player $player): bool{
        return isset($this->players[$player->getName()]);
    }

    public function get(string $player): int{
        return $this->players[$player] ?? 0;
    }

    public function close(Player $player): void{
        if ($this->has($player)) {
            unset($this->players[$player->getName()]);
        }
    }

    public function checkPlayer(Player $player): int{
        if (!$this->has($player)) return 0;
        if ($this->players[$player->getName()]-- <= 0){
            $player->sendMessage(LanguageFactory::getInstance()->getTranslation('pvp-timer-ended'));
            SessionFactory::getInstance()->get($player->getName())->getData()['invincible-time'] = $this->get($player->getName());
            $this->close($player);
            return 0;
        } else {
            return $this->players[$player->getName()] ?? 0;
        }
    }
}