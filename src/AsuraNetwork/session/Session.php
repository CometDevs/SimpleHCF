<?php

declare(strict_types=1);

namespace AsuraNetwork\session;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\Loader;
use AsuraNetwork\session\exception\PlayerNonOnlineException;
use AsuraNetwork\session\modules\InviteModule;
use AsuraNetwork\session\modules\Module;
use AsuraNetwork\session\modules\ModuleIds;
use AsuraNetwork\session\modules\StatsModule;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class Session
 * @package AsuraNetwork\session
 */
class Session{

    /** @var Faction|null */
    private ?Faction $faction = null;
    private ?FactionRole $role = null;
    private string $name;
    private array $data;
    private array $modules = [];

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

    public function getXuid(): string{
        return $this->data['xuid'];
    }

    public function getUuid(): string{
        return $this->data['uuid'];
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
     * @return FactionRole|null
     */
    public function getRole(): ?FactionRole{
        return $this->role;
    }

    public function hasFaction(): bool{
        return $this->faction !== null;
    }

    /**
     * @param Faction|null $faction
     */
    public function setFaction(?Faction $faction): void{
        $this->faction = $faction;
        $this->save();
    }

    /**
     * @param FactionRole|null $role
     */
    public function setRole(?FactionRole $role): void{
        $this->role = $role;
        $this->save();
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

    public function sendMessage(string $message): void{
        $this->getPlayer()?->sendMessage(TextFormat::colorize($message));
    }

    public function sendTranslation(string $translation, array $params = []): void{
        $this->getPlayer()?->sendMessage(LanguageFactory::getInstance()->getTranslation($translation, $params));
    }

    public function init(): void{
        $this->modules[ModuleIds::INVITE] = new InviteModule($this);
        $this->modules[ModuleIds::STATS] = new StatsModule($this, $this->getData()['kills'], $this->getData()['deaths']);
    }

    public function getModule(string $id): ?Module{
        return $this->modules[$id] ?? null;
    }

    public function getInvitesModule(): InviteModule{
        return $this->modules[ModuleIds::INVITE];
    }

    public function getStatsModule(): StatsModule{
        return $this->modules[ModuleIds::STATS];
    }

    public function save(): Session{
        file_put_contents(Loader::getInstance()->getDataFolder() . 'players/' . $this->getName() . '.yml', yaml_emit([
            "xuid" => $this->getXuid(),
            "uid" => $this->getUuid(),
            "faction" => $this->getFaction()?->getName(),
            "faction-role" => $this->getRole()?->name(),
            "invincible-time" => 3600,
            "kills" => $this->getStatsModule()->getKills(),
            "deaths" => $this->getStatsModule()->getDeaths(),
            "cooldowns" => []
        ]));
        return $this;
    }
}
