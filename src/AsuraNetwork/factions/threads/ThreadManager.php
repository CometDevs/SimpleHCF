<?php

namespace AsuraNetwork\factions\threads;

use AsuraNetwork\factions\Faction;
use AsuraNetwork\Loader;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Webmozart\PathUtil\Path;

class ThreadManager{
    use SingletonTrait;

    private LogThread $factionLog;

    public function init(): void{
        @mkdir(Server::getInstance()->getDataPath() . 'logs');
        $path = Path::join(Server::getInstance()->getDataPath(), 'logs');
        $this->factionLog = new LogThread(Path::join($path, 'factions.log'));
        $this->factionLog->start();
    }

    public function close(): void{
        $this->factionLog->shutdown();
    }

    public function factionLog(Faction $faction, string $action): void{
        $this->factionLog->write("[{$faction->getName()} thread/ACTION]: $action");
    }

}