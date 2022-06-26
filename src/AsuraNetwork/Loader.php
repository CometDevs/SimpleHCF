<?php

namespace AsuraNetwork;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\factions\listener\FactionListener;
use AsuraNetwork\session\listener\SessionListener;
use AsuraNetwork\session\SessionFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase{
    use SingletonTrait;

    public function onEnable(): void{
        $this->saveConfig();
        date_default_timezone_set($this->getConfig()->get("time-zone", "America/Chicago"));

        $this->saveResource("abilities.yml");
        $this->saveResource("cooldowns.yml");
        $this->saveResource("faction.yml");

        FactionsFactory::getInstance()->init();
        SessionFactory::getInstance()->init();
        // Please don't use method static with main, use SingletonTrait
        EconomyFactory::getInstance()->init();
        $this->initListeners();
    }

    private function initListeners(): void{
        foreach ([new SessionListener(), new FactionListener()] as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}